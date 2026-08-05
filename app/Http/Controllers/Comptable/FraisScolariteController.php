<?php
// app/Http/Controllers/Comptable/FraisScolariteController.php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\FraisScolarite;
use App\Models\AnneeScolaire;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FraisScolariteController extends Controller
{
    /**
     * Affiche la liste des frais de scolarité
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        $query = FraisScolarite::where('etablissement_id', $etablissementId)
            ->with('anneeScolaire');
        
        // Filtres
        if ($request->filled('annee_scolaire_id')) {
            $query->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('obligatoire')) {
            $query->where('obligatoire', $request->obligatoire);
        }
        
        $frais = $query->orderBy('libelle')->paginate(15);
        
        $anneesScolaires = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->orderBy('libelle', 'desc')
            ->get();
        
        $types = [
            'inscription' => 'Inscription',
            'scolarite' => 'Scolarité',
            'cantine' => 'Cantine',
            'transport' => 'Transport',
            'autre' => 'Autre'
        ];
        
        return view('comptable.frais.index', compact('frais', 'anneesScolaires', 'types'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        $anneesScolaires = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->orderBy('libelle', 'desc')
            ->get();
        
        // Récupérer toutes les classes
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $types = [
            'inscription' => 'Inscription',
            'scolarite' => 'Scolarité',
            'cantine' => 'Cantine',
            'transport' => 'Transport',
            'autre' => 'Autre'
        ];
        
        $periodicites = [
            'mensuel' => 'Mensuel',
            'trimestriel' => 'Trimestriel',
            'annuel' => 'Annuel',
            'unique' => 'Unique'
        ];
        
        return view('comptable.frais.create', compact('anneesScolaires', 'classes', 'types', 'periodicites'));
    }

    /**
     * Enregistre un nouveau frais
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;

        $validator = Validator::make($request->all(), [
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'classe_id' => 'nullable|exists:classes,id',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'required|numeric|min:1',
            'type' => 'required|in:inscription,scolarite,cantine,transport,autre',
            'periodicite' => 'required|in:mensuel,trimestriel,annuel,unique',
            'obligatoire' => 'boolean',
            'echeances' => 'nullable|array',
            'echeances.*.libelle' => 'required_with:echeances|string',
            'echeances.*.montant' => 'required_with:echeances|numeric|min:0',
            'echeances.*.date_limite' => 'required_with:echeances|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'année scolaire appartient à l'établissement
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->find($request->annee_scolaire_id);
        
        if (!$anneeScolaire) {
            return redirect()->back()
                ->with('error', "L'année scolaire sélectionnée n'est pas valide.")
                ->withInput();
        }

        // Vérifier la classe si spécifiée
        if ($request->filled('classe_id')) {
            $classe = Classe::where('etablissement_id', $etablissementId)
                ->find($request->classe_id);
            
            if (!$classe) {
                return redirect()->back()
                    ->with('error', "La classe sélectionnée n'est pas valide.")
                    ->withInput();
            }
        }

        DB::beginTransaction();

        try {
            // Créer le frais
            $frais = FraisScolarite::create([
                'etablissement_id' => $etablissementId,
                'annee_scolaire_id' => $request->annee_scolaire_id,
                'classe_id' => $request->classe_id,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'montant' => $request->montant,
                'type' => $request->type,
                'periodicite' => $request->periodicite,
                'obligatoire' => $request->boolean('obligatoire', true),
            ]);

            // Enregistrer les échéances si présentes
            if ($request->has('echeances')) {
                foreach ($request->echeances as $echeance) {
                    if (!empty($echeance['libelle']) && !empty($echeance['montant'])) {
                        // Créer les échéances liées au frais
                        // À implémenter selon votre structure de table echeances_frais
                        // \App\Models\EcheanceFrais::create([
                        //     'frais_id' => $frais->id,
                        //     'libelle' => $echeance['libelle'],
                        //     'montant' => $echeance['montant'],
                        //     'date_limite' => $echeance['date_limite']
                        // ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('comptable.frais.index')
                ->with('success', 'Frais de scolarité créé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'un frais
     */
    public function show($id)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        $frais = FraisScolarite::where('etablissement_id', $etablissementId)
            ->with(['anneeScolaire', 'classe'])
            ->withCount('paiements')
            ->findOrFail($id);
        
        // Statistiques
        $stats = [
            'total_paye' => $frais->paiements()->sum('montant'),
            'nombre_paiements' => $frais->paiements()->count(),
            'moyenne' => $frais->paiements()->avg('montant') ?? 0,
        ];
        
        return view('comptable.frais.show', compact('frais', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        $frais = FraisScolarite::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        $anneesScolaires = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->orderBy('libelle', 'desc')
            ->get();
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $types = [
            'inscription' => 'Inscription',
            'scolarite' => 'Scolarité',
            'cantine' => 'Cantine',
            'transport' => 'Transport',
            'autre' => 'Autre'
        ];
        
        $periodicites = [
            'mensuel' => 'Mensuel',
            'trimestriel' => 'Trimestriel',
            'annuel' => 'Annuel',
            'unique' => 'Unique'
        ];
        
        return view('comptable.frais.edit', compact('frais', 'anneesScolaires', 'classes', 'types', 'periodicites'));
    }

    /**
     * Met à jour un frais
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        $frais = FraisScolarite::where('etablissement_id', $etablissementId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'classe_id' => 'nullable|exists:classes,id',
            'libelle' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'required|numeric|min:1',
            'type' => 'required|in:inscription,scolarite,cantine,transport,autre',
            'periodicite' => 'required|in:mensuel,trimestriel,annuel,unique',
            'obligatoire' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier l'année scolaire
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->find($request->annee_scolaire_id);
        
        if (!$anneeScolaire) {
            return redirect()->back()
                ->with('error', "L'année scolaire sélectionnée n'est pas valide.")
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $frais->update([
                'annee_scolaire_id' => $request->annee_scolaire_id,
                'classe_id' => $request->classe_id,
                'libelle' => $request->libelle,
                'description' => $request->description,
                'montant' => $request->montant,
                'type' => $request->type,
                'periodicite' => $request->periodicite,
                'obligatoire' => $request->boolean('obligatoire', true),
            ]);

            DB::commit();

            return redirect()->route('comptable.frais.index')
                ->with('success', 'Frais de scolarité mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Supprime un frais
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        $frais = FraisScolarite::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        // Vérifier si des paiements sont liés
        if ($frais->paiements()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer ce frais car des paiements y sont liés.');
        }

        $frais->delete();

        return redirect()->route('comptable.frais.index')
            ->with('success', 'Frais de scolarité supprimé avec succès.');
    }

    /**
     * Duplique un frais pour une nouvelle année
     */
    public function duplicate(Request $request, $id)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        $fraisOriginal = FraisScolarite::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'année cible n'a pas déjà ce frais
        $existe = FraisScolarite::where('etablissement_id', $etablissementId)
            ->where('annee_scolaire_id', $request->annee_scolaire_id)
            ->where('libelle', $fraisOriginal->libelle)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Un frais avec le même libellé existe déjà pour cette année.');
        }

        FraisScolarite::create([
            'etablissement_id' => $etablissementId,
            'annee_scolaire_id' => $request->annee_scolaire_id,
            'classe_id' => $fraisOriginal->classe_id,
            'libelle' => $fraisOriginal->libelle,
            'description' => $fraisOriginal->description,
            'montant' => $fraisOriginal->montant,
            'type' => $fraisOriginal->type,
            'periodicite' => $fraisOriginal->periodicite,
            'obligatoire' => $fraisOriginal->obligatoire,
        ]);

        return redirect()->route('comptable.frais.index')
            ->with('success', 'Frais dupliqué avec succès pour la nouvelle année.');
    }
}