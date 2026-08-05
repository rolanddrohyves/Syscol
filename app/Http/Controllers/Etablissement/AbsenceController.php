<?php
// app/Http/Controllers/Etablissement/AbsenceController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
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
        
        if ($request->filled('justifiee')) {
            $query->where('justifiee', $request->justifiee);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        
        $absences = $query->orderBy('date', 'desc')->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        // ✅ STATISTIQUES POUR LES CARTES
        $stats = [
            'total' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count(),
            'aujourdhui' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->whereDate('date', Carbon::today())
                ->count(),
            'justifiees' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('justifiee', true)
                ->count(),
            'non_justifiees' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('justifiee', false)
                ->count(),
        ];
        
        return view('etablissement.absences.index', compact('absences', 'classes', 'stats'));
    }

    /**
     * Affiche le formulaire de signalement d'absence
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $eleveId = $request->get('eleve_id');
        $classeId = $request->get('classe_id');
        
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
                ->get(['id', 'nom', 'prenom']);
        }
        
        return view('etablissement.absences.create', compact(
            'classes', 
            'eleveId', 
            'classeId',
            'elevesParClasse'
        ));
    }

    /**
     * Enregistre une absence
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'type' => 'required|in:absence,retard,sortie_anticipée',
            'motif' => 'nullable|string|max:255',
            'heure' => 'nullable|date_format:H:i',
        ]);

        // Vérifier que l'élève appartient bien à la classe
        $eleve = Eleve::find($validated['eleve_id']);
        if ($eleve->classe_id != $validated['classe_id']) {
            return redirect()->back()
                ->with('error', 'L\'élève n\'appartient pas à la classe sélectionnée.')
                ->withInput();
        }

        $absence = Absence::create([
            'eleve_id' => $validated['eleve_id'],
            'date' => $validated['date'],
            'type' => $validated['type'],
            'motif' => $validated['motif'],
            'heure' => $validated['heure'] ?? null,
            'justifiee' => false,
        ]);

        return redirect()->route('etablissement.absences.index')
            ->with('success', 'Absence signalée avec succès.');
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show($id)
    {
        $absence = Absence::with(['eleve', 'eleve.classe'])->findOrFail($id);
        
        return view('etablissement.absences.show', compact('absence'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $absence = Absence::findOrFail($id);
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
                ->get(['id', 'nom', 'prenom']);
        }
        
        return view('etablissement.absences.edit', compact('absence', 'classes', 'elevesParClasse'));
    }

    /**
     * Met à jour une absence
     */
    public function update(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        
        $validated = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'type' => 'required|in:absence,retard,sortie_anticipée',
            'motif' => 'nullable|string|max:255',
            'heure' => 'nullable|date_format:H:i',
            'justifiee' => 'boolean',
        ]);

        // Vérifier la cohérence élève/classe
        $eleve = Eleve::find($validated['eleve_id']);
        if ($eleve->classe_id != $validated['classe_id']) {
            return redirect()->back()
                ->with('error', 'L\'élève n\'appartient pas à la classe sélectionnée.')
                ->withInput();
        }

        $absence->update($validated);

        return redirect()->route('etablissement.absences.index')
            ->with('success', 'Absence mise à jour avec succès.');
    }

    /**
     * Supprime une absence
     */
    public function destroy($id)
    {
        $absence = Absence::findOrFail($id);
        $absence->delete();

        return redirect()->route('etablissement.absences.index')
            ->with('success', 'Absence supprimée avec succès.');
    }

    /**
     * Justifie une absence
     */
    public function justify(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        
        $validated = $request->validate([
            'justification' => 'required|string|max:500',
        ]);

        $absence->update([
            'justifiee' => true,
            'motif' => $validated['justification'],
        ]);

        return redirect()->route('etablissement.absences.index')
            ->with('success', 'Absence justifiée avec succès.');
    }

    /**
     * Export des absences
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $absences = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe'])
            ->orderBy('date', 'desc')
            ->get();
        
        $filename = 'absences-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['Date', 'Élève', 'Classe', 'Type', 'Motif', 'Justifiée', 'Heure']);
        
        foreach ($absences as $absence) {
            fputcsv($handle, [
                $absence->date->format('d/m/Y'),
                $absence->eleve->prenom . ' ' . $absence->eleve->nom,
                $absence->eleve->classe->nom,
                $absence->type,
                $absence->motif ?? '',
                $absence->justifiee ? 'Oui' : 'Non',
                $absence->heure ?? '',
            ]);
        }
        
        fclose($handle);
        exit;
    }
}