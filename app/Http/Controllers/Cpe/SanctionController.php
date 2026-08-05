<?php
// app/Http/Controllers/Cpe/SanctionController.php

namespace App\Http\Controllers\Cpe;

use App\Http\Controllers\Controller;
use App\Models\Sanction;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SanctionController extends Controller
{
    /**
     * Affiche la liste des sanctions
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Sanction::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe']);
        
        // Filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $sanctions = $query->orderBy('date', 'desc')->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        // Types de sanctions pour les filtres
        $types = [
            'avertissement' => 'Avertissement',
            'retenue' => 'Retenue',
            'exclusion_temporaire' => 'Exclusion temporaire',
            'exclusion_definitive' => 'Exclusion définitive',
            'travail_extra' => 'Travail supplémentaire'
        ];
        
        // Statuts pour les filtres
        $statuts = [
            'en_cours' => 'En cours',
            'executee' => 'Exécutée',
            'annulee' => 'Annulée'
        ];
        
        // Statistiques
        $stats = [
            'total' => Sanction::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count(),
            'en_cours' => Sanction::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('statut', 'en_cours')
                ->count(),
            'ce_mois' => Sanction::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->whereMonth('date', Carbon::now()->month)
                ->count(),
        ];
        
        return view('cpe.sanctions.index', compact('sanctions', 'classes', 'types', 'statuts', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'une sanction
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
                ->get(['id', 'prenom', 'nom']);
        }
        
        $types = [
            'avertissement' => 'Avertissement',
            'retenue' => 'Retenue',
            'exclusion_temporaire' => 'Exclusion temporaire',
            'exclusion_definitive' => 'Exclusion définitive',
            'travail_extra' => 'Travail supplémentaire'
        ];
        
        $statuts = [
            'en_cours' => 'En cours',
            'executee' => 'Exécutée',
            'annulee' => 'Annulée'
        ];
        
        return view('cpe.sanctions.create', compact('classes', 'elevesParClasse', 'eleveId', 'classeId', 'types', 'statuts'));
    }

    /**
     * Enregistre une nouvelle sanction
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'type' => 'required|string',
            'date' => 'required|date',
            'motif' => 'required|string|max:500',
            'description' => 'nullable|string',
            'duree' => 'nullable|integer|min:1',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'nullable|string|in:en_cours,executee,annulee',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'élève appartient bien à la classe
        $eleve = Eleve::with('classe')->find($request->eleve_id);
        if ($eleve->classe_id != $request->classe_id) {
            return redirect()->back()
                ->with('error', "L'élève n'appartient pas à la classe sélectionnée.")
                ->withInput();
        }

        Sanction::create([
            'eleve_id' => $request->eleve_id,
            'type' => $request->type,
            'date' => $request->date,
            'motif' => $request->motif,
            'description' => $request->description,
            'duree' => $request->duree,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->statut ?? 'en_cours',
        ]);

        return redirect()->route('cpe.sanctions.index')
            ->with('success', 'Sanction enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une sanction
     */
    public function show($id)
    {
        $sanction = Sanction::with(['eleve', 'eleve.classe'])->findOrFail($id);
        
        return view('cpe.sanctions.show', compact('sanction'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $sanction = Sanction::findOrFail($id);
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
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
        
        $types = [
            'avertissement' => 'Avertissement',
            'retenue' => 'Retenue',
            'exclusion_temporaire' => 'Exclusion temporaire',
            'exclusion_definitive' => 'Exclusion définitive',
            'travail_extra' => 'Travail supplémentaire'
        ];
        
        $statuts = [
            'en_cours' => 'En cours',
            'executee' => 'Exécutée',
            'annulee' => 'Annulée'
        ];
        
        return view('cpe.sanctions.edit', compact('sanction', 'classes', 'elevesParClasse', 'types', 'statuts'));
    }

    /**
     * Met à jour une sanction
     */
    public function update(Request $request, $id)
    {
        $sanction = Sanction::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'type' => 'required|string',
            'date' => 'required|date',
            'motif' => 'required|string|max:500',
            'description' => 'nullable|string',
            'duree' => 'nullable|integer|min:1',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'nullable|string|in:en_cours,executee,annulee',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'élève appartient bien à la classe
        $eleve = Eleve::find($request->eleve_id);
        if ($eleve->classe_id != $request->classe_id) {
            return redirect()->back()
                ->with('error', "L'élève n'appartient pas à la classe sélectionnée.")
                ->withInput();
        }

        $sanction->update([
            'eleve_id' => $request->eleve_id,
            'type' => $request->type,
            'date' => $request->date,
            'motif' => $request->motif,
            'description' => $request->description,
            'duree' => $request->duree,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->statut ?? 'en_cours',
        ]);

        return redirect()->route('cpe.sanctions.index')
            ->with('success', 'Sanction mise à jour avec succès.');
    }

    /**
     * Supprime une sanction
     */
    public function destroy($id)
    {
        $sanction = Sanction::findOrFail($id);
        $sanction->delete();

        return redirect()->route('cpe.sanctions.index')
            ->with('success', 'Sanction supprimée avec succès.');
    }

    /**
     * Export des sanctions au format CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Sanction::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe']);
        
        // Appliquer les filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', fn($q) => $q->where('classe_id', $request->classe_id));
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $sanctions = $query->orderBy('date', 'desc')->get();
        
        // Générer le fichier CSV
        $filename = 'sanctions_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // BOM UTF-8 pour Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // En-têtes
        fputcsv($handle, ['Date', 'Élève', 'Classe', 'Type', 'Motif', 'Statut', 'Durée', 'Date début', 'Date fin']);
        
        // Données
        foreach ($sanctions as $sanction) {
            fputcsv($handle, [
                $sanction->date->format('d/m/Y'),
                $sanction->eleve->prenom . ' ' . $sanction->eleve->nom,
                $sanction->eleve->classe->nom,
                $this->getTypeLabel($sanction->type),
                $sanction->motif,
                $this->getStatutLabel($sanction->statut),
                $sanction->duree ? $sanction->duree . ' heures' : '',
                $sanction->date_debut ? $sanction->date_debut->format('d/m/Y') : '',
                $sanction->date_fin ? $sanction->date_fin->format('d/m/Y') : '',
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Met à jour le statut d'une sanction
     */
    public function updateStatut(Request $request, $id)
    {
        $sanction = Sanction::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'statut' => 'required|string|in:en_cours,executee,annulee',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $sanction->update(['statut' => $request->statut]);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour']);
    }

    /**
     * Récupère le libellé du type
     */
    private function getTypeLabel($type)
    {
        $labels = [
            'avertissement' => 'Avertissement',
            'retenue' => 'Retenue',
            'exclusion_temporaire' => 'Exclusion temporaire',
            'exclusion_definitive' => 'Exclusion définitive',
            'travail_extra' => 'Travail supplémentaire'
        ];
        
        return $labels[$type] ?? $type;
    }

    /**
     * Récupère le libellé du statut
     */
    private function getStatutLabel($statut)
    {
        $labels = [
            'en_cours' => 'En cours',
            'executee' => 'Exécutée',
            'annulee' => 'Annulée'
        ];
        
        return $labels[$statut] ?? $statut;
    }
}