<?php
// app/Http/Controllers/Cpe/RetardController.php

namespace App\Http\Controllers\Cpe;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RetardController extends Controller
{
    /**
     * Affiche la liste des retards
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('type', 'retard')
            ->with(['eleve', 'eleve.classe']);
        
        // Filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $retards = $query->orderBy('date', 'desc')
            ->orderBy('heure', 'desc')
            ->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        // Statistiques
        $stats = [
            'total' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('type', 'retard')
                ->count(),
            'aujourdhui' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('type', 'retard')
                ->whereDate('date', Carbon::today())
                ->count(),
            'ce_mois' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('type', 'retard')
                ->whereMonth('date', Carbon::now()->month)
                ->count(),
        ];
        
        return view('cpe.retards.index', compact('retards', 'classes', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'un retard
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
        
        return view('cpe.retards.create', compact('classes', 'elevesParClasse', 'eleveId', 'classeId'));
    }

    /**
     * Enregistre un nouveau retard
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'heure' => 'required|date_format:H:i',
            'motif' => 'nullable|string|max:255',
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

        // Vérifier les doublons (même élève, même jour)
        $existe = Absence::where('eleve_id', $request->eleve_id)
            ->where('type', 'retard')
            ->whereDate('date', $request->date)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Un retard existe déjà pour cet élève à cette date.')
                ->withInput();
        }

        Absence::create([
            'eleve_id' => $request->eleve_id,
            'date' => $request->date,
            'type' => 'retard',
            'heure' => $request->heure,
            'motif' => $request->motif,
            'justifiee' => false,
        ]);

        return redirect()->route('cpe.retards.index')
            ->with('success', 'Retard signalé avec succès.');
    }

    /**
     * Affiche les détails d'un retard
     */
    public function show($id)
    {
        $retard = Absence::where('type', 'retard')
            ->with(['eleve', 'eleve.classe'])
            ->findOrFail($id);
        
        return view('cpe.retards.show', compact('retard'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $retard = Absence::where('type', 'retard')->findOrFail($id);
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        $elevesParClasse = [];
        foreach ($classes as $classe) {
            $elevesParClasse[$classe->id] = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get(['id', 'prenom', 'nom']);
        }
        
        return view('cpe.retards.edit', compact('retard', 'classes', 'elevesParClasse'));
    }

    /**
     * Met à jour un retard
     */
    public function update(Request $request, $id)
    {
        $retard = Absence::where('type', 'retard')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'heure' => 'required|date_format:H:i',
            'motif' => 'nullable|string|max:255',
            'justifiee' => 'boolean',
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

        // Vérifier les doublons (sauf pour ce retard)
        $existe = Absence::where('eleve_id', $request->eleve_id)
            ->where('type', 'retard')
            ->whereDate('date', $request->date)
            ->where('id', '!=', $id)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Un retard existe déjà pour cet élève à cette date.')
                ->withInput();
        }

        $retard->update([
            'eleve_id' => $request->eleve_id,
            'date' => $request->date,
            'heure' => $request->heure,
            'motif' => $request->motif,
            'justifiee' => $request->boolean('justifiee', false),
        ]);

        return redirect()->route('cpe.retards.index')
            ->with('success', 'Retard mis à jour avec succès.');
    }

    /**
     * Supprime un retard
     */
    public function destroy($id)
    {
        $retard = Absence::where('type', 'retard')->findOrFail($id);
        $retard->delete();

        return redirect()->route('cpe.retards.index')
            ->with('success', 'Retard supprimé avec succès.');
    }

    /**
     * Export des retards au format CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('type', 'retard')
            ->with(['eleve', 'eleve.classe']);
        
        // Appliquer les filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', fn($q) => $q->where('classe_id', $request->classe_id));
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $retards = $query->orderBy('date', 'desc')->get();
        
        // Générer le fichier CSV
        $filename = 'retards_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // BOM UTF-8 pour Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // En-têtes
        fputcsv($handle, ['Date', 'Heure', 'Élève', 'Classe', 'Motif', 'Justifié', 'Enregistré le']);
        
        // Données
        foreach ($retards as $retard) {
            fputcsv($handle, [
                $retard->date->format('d/m/Y'),
                $retard->heure ?? '',
                $retard->eleve->prenom . ' ' . $retard->eleve->nom,
                $retard->eleve->classe->nom,
                $retard->motif ?? '',
                $retard->justifiee ? 'Oui' : 'Non',
                $retard->created_at->format('d/m/Y H:i')
            ]);
        }
        
        fclose($handle);
        exit;
    }
}