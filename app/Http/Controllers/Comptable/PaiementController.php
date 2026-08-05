<?php
// app/Http/Controllers/Comptable/PaiementController.php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Eleve;
use App\Models\FraisScolarite;
use App\Models\Classe;
use App\Models\AnneeScolaire;
use App\Services\EcheanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaiementController extends Controller
{
    /**
     * Affiche la liste des paiements
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe', 'frais']);
        
        // Filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('mode')) {
            $query->where('mode_paiement', $request->mode);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_paiement', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_paiement', '<=', $request->date_fin);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('eleve', function($q) use ($search) {
                $q->where('prenom', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }
        
        $paiements = $query->orderBy('date_paiement', 'desc')->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        // Statistiques
        $stats = [
            'total' => Paiement::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->sum('montant'),
            'mois' => Paiement::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->whereMonth('date_paiement', now()->month)
                ->sum('montant'),
            'nombre' => Paiement::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count(),
            'moyenne' => Paiement::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->avg('montant') ?? 0,
        ];
        
        return view('comptable.paiements.index', compact('paiements', 'classes', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $eleveId = $request->get('eleve_id');
        $classeId = $request->get('classe_id');
        
        // Récupérer toutes les classes
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        // Préparer les élèves par classe pour le JavaScript
        $elevesParClasse = [];
        foreach ($classes as $classe) {
            $elevesParClasse[$classe->id] = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get(['id', 'prenom', 'nom', 'matricule']);
        }
        
        // Récupérer tous les frais de scolarité
        $tousFrais = FraisScolarite::where('etablissement_id', $etablissementId)
            ->orderBy('libelle')
            ->get();
        
        // Organiser les frais par classe pour le JavaScript
        $fraisParClasse = [];
        foreach ($tousFrais as $frais) {
            $key = $frais->classe_id ?? 'toutes';
            if (!isset($fraisParClasse[$key])) {
                $fraisParClasse[$key] = [];
            }
            $fraisParClasse[$key][] = [
                'id' => $frais->id,
                'libelle' => $frais->libelle,
                'montant' => $frais->montant,
                'obligatoire' => $frais->obligatoire,
                'periodicite' => $frais->periodicite,
                'type' => $frais->type
            ];
        }
        
        $modes = [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte bancaire',
            'mobile_money' => 'Mobile Money'
        ];
        
        return view('comptable.paiements.create', compact('classes', 'elevesParClasse', 'fraisParClasse', 'eleveId', 'classeId', 'modes'));
    }

    /**
     * Enregistre un nouveau paiement
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'frais_id' => 'required|exists:frais_scolarites,id',
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string|max:100',
            'statut' => 'required|in:paye,en_attente,partiel,annule',
            'commentaire' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'élève appartient bien à la classe
        $eleve = Eleve::find($request->eleve_id);
        if (!$eleve || $eleve->classe_id != $request->classe_id) {
            return redirect()->back()
                ->with('error', "L'élève n'appartient pas à la classe sélectionnée.")
                ->withInput();
        }

        // Vérifier que le frais existe
        $frais = FraisScolarite::find($request->frais_id);
        if (!$frais) {
            return redirect()->back()
                ->with('error', "Le frais sélectionné n'existe pas.")
                ->withInput();
        }

        // Générer une référence unique si non fournie
        $reference = $request->reference;
        if (!$reference) {
            $maxId = Paiement::max('id') ?? 0;
            $reference = 'PAI-' . date('Ymd') . '-' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
        }

        DB::beginTransaction();
        
        try {
            $paiement = Paiement::create([
                'eleve_id' => $request->eleve_id,
                'frais_id' => $request->frais_id,
                'montant' => $request->montant,
                'montant_paye' => $request->montant,
                'montant_restant' => 0,
                'date_paiement' => $request->date_paiement,
                'date_echeance' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'reference' => $reference,
                'statut' => $request->statut,
                'commentaire' => $request->commentaire,
                'cree_par' => auth()->id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('comptable.paiements.index')
                ->with('success', 'Paiement enregistré avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'un paiement avec situation financière complète
     */
    public function show($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $paiement = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe', 'frais', 'frais.anneeScolaire'])
            ->findOrFail($id);
        
        // Récupérer la situation financière
        $echeanceService = new EcheanceService();
        $situation = $echeanceService->getSituationFinanciere($paiement->eleve);
        
        return view('comptable.paiements.show', compact('paiement', 'situation'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $paiement = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'frais'])
            ->findOrFail($id);
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $elevesParClasse = [];
        foreach ($classes as $classe) {
            $elevesParClasse[$classe->id] = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get(['id', 'prenom', 'nom']);
        }
        
        $tousFrais = FraisScolarite::where('etablissement_id', $etablissementId)
            ->orderBy('libelle')
            ->get();
        
        $fraisParClasse = [];
        foreach ($tousFrais as $frais) {
            $key = $frais->classe_id ?? 'toutes';
            if (!isset($fraisParClasse[$key])) {
                $fraisParClasse[$key] = [];
            }
            $fraisParClasse[$key][] = $frais;
        }
        
        $modes = [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte bancaire',
            'mobile_money' => 'Mobile Money'
        ];
        
        return view('comptable.paiements.edit', compact('paiement', 'classes', 'elevesParClasse', 'fraisParClasse', 'modes'));
    }

    /**
     * Met à jour un paiement
     */
    public function update(Request $request, $id)
    {
        $paiement = Paiement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'frais_id' => 'required|exists:frais_scolarites,id',
            'montant' => 'required|numeric|min:1',
            'date_paiement' => 'required|date',
            'mode_paiement' => 'required|string',
            'reference' => 'nullable|string|max:100',
            'statut' => 'required|in:paye,en_attente,partiel,annule',
            'commentaire' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'élève appartient bien à la classe
        $eleve = Eleve::find($request->eleve_id);
        if (!$eleve || $eleve->classe_id != $request->classe_id) {
            return redirect()->back()
                ->with('error', "L'élève n'appartient pas à la classe sélectionnée.")
                ->withInput();
        }

        DB::beginTransaction();
        
        try {
            $paiement->update([
                'eleve_id' => $request->eleve_id,
                'frais_id' => $request->frais_id,
                'montant' => $request->montant,
                'date_paiement' => $request->date_paiement,
                'mode_paiement' => $request->mode_paiement,
                'reference' => $request->reference,
                'statut' => $request->statut,
                'commentaire' => $request->commentaire,
            ]);
            
            DB::commit();
            
            return redirect()->route('comptable.paiements.index')
                ->with('success', 'Paiement mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime un paiement
     */
    public function destroy($id)
    {
        $paiement = Paiement::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $paiement->delete();
            DB::commit();
            
            return redirect()->route('comptable.paiements.index')
                ->with('success', 'Paiement supprimé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Export des paiements au format CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe', 'frais']);
        
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', fn($q) => $q->where('classe_id', $request->classe_id));
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('mode')) {
            $query->where('mode_paiement', $request->mode);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date_paiement', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date_paiement', '<=', $request->date_fin);
        }
        
        $paiements = $query->orderBy('date_paiement', 'desc')->get();
        
        $filename = 'paiements_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($handle, ['Date', 'Élève', 'Classe', 'Matricule', 'Frais', 'Montant (FCFA)', 'Mode', 'Statut', 'Référence', 'Commentaire']);
        
        foreach ($paiements as $paiement) {
            fputcsv($handle, [
                $paiement->date_paiement->format('d/m/Y'),
                $paiement->eleve->prenom . ' ' . $paiement->eleve->nom,
                $paiement->eleve->classe->nom ?? '',
                $paiement->eleve->matricule ?? '',
                $paiement->frais->libelle ?? '',
                number_format($paiement->montant, 0, ',', ' '),
                $this->getModeLabel($paiement->mode_paiement),
                $this->getStatutLabel($paiement->statut),
                $paiement->reference ?? '',
                $paiement->commentaire ?? '',
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Génère un reçu de paiement
     */
    public function recu($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $paiement = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe', 'frais', 'frais.anneeScolaire'])
            ->findOrFail($id);
        
        $etablissement = \App\Models\Etablissement::find($etablissementId);
        
        // Récupérer la situation financière pour le reçu
        $echeanceService = new EcheanceService();
        $situation = $echeanceService->getSituationFinanciere($paiement->eleve);
        
        return view('comptable.paiements.recu', compact('paiement', 'etablissement', 'situation'));
    }

    /**
     * Récupère le libellé du mode de paiement
     */
    private function getModeLabel($mode)
    {
        $modes = [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte bancaire',
            'mobile_money' => 'Mobile Money'
        ];
        
        return $modes[$mode] ?? ucfirst($mode);
    }

    /**
     * Récupère le libellé du statut
     */
    private function getStatutLabel($statut)
    {
        $statuts = [
            'paye' => 'Payé',
            'en_attente' => 'En attente',
            'partiel' => 'Partiel',
            'annule' => 'Annulé',
        ];
        
        return $statuts[$statut] ?? ucfirst($statut);
    }
}