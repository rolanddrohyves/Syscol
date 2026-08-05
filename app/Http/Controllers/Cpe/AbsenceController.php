<?php
// app/Http/Controllers/Cpe/AbsenceController.php

namespace App\Http\Controllers\Cpe;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        if ($request->filled('justifiee')) {
            $query->where('justifiee', $request->justifiee);
        }
        
        $absences = $query->orderBy('date', 'desc')->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        // Statistiques
        $stats = [
            'total' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count(),
            'aujourdhui' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->whereDate('date', Carbon::today())
                ->count(),
            'non_justifiees' => Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('justifiee', false)
                ->count(),
        ];
        
        return view('cpe.absences.index', compact('absences', 'classes', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'une absence
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
        
        return view('cpe.absences.create', compact('classes', 'elevesParClasse', 'eleveId', 'classeId'));
    }

    /**
     * Enregistre une nouvelle absence
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'type' => 'required|in:absence,retard,sortie_anticipée',
            'motif' => 'nullable|string|max:255',
            'heure' => 'nullable|date_format:H:i',
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

        // Vérifier les doublons (même élève, même jour, même type)
        $existe = Absence::where('eleve_id', $request->eleve_id)
            ->whereDate('date', $request->date)
            ->where('type', $request->type)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Une absence de ce type existe déjà pour cet élève à cette date.')
                ->withInput();
        }

        // ✅ CORRECTION : Ajouter classe_id dans la création
        Absence::create([
            'eleve_id' => $request->eleve_id,
            'classe_id' => $request->classe_id, // ← AJOUTÉ
            'date' => $request->date,
            'type' => $request->type,
            'motif' => $request->motif,
            'heure' => $request->heure,
            'justifiee' => false,
        ]);

        return redirect()->route('cpe.absences.index')
            ->with('success', 'Absence signalée avec succès.');
    }

    /**
     * Affiche les détails d'une absence
     */
    public function show($id)
    {
        $absence = Absence::with(['eleve', 'eleve.classe'])->findOrFail($id);
        
        return view('cpe.absences.show', compact('absence'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $absence = Absence::findOrFail($id);
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
        
        return view('cpe.absences.edit', compact('absence', 'classes', 'elevesParClasse'));
    }

    /**
     * Met à jour une absence
     */
    public function update(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'type' => 'required|in:absence,retard,sortie_anticipée',
            'motif' => 'nullable|string|max:255',
            'heure' => 'nullable|date_format:H:i',
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

        // Vérifier les doublons (sauf pour cette absence)
        $existe = Absence::where('eleve_id', $request->eleve_id)
            ->whereDate('date', $request->date)
            ->where('type', $request->type)
            ->where('id', '!=', $id)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->with('error', 'Une absence de ce type existe déjà pour cet élève à cette date.')
                ->withInput();
        }

        $absence->update([
            'eleve_id' => $request->eleve_id,
            'classe_id' => $request->classe_id, // ← AJOUTÉ pour la mise à jour aussi
            'date' => $request->date,
            'type' => $request->type,
            'motif' => $request->motif,
            'heure' => $request->heure,
            'justifiee' => $request->boolean('justifiee', false),
        ]);

        return redirect()->route('cpe.absences.index')
            ->with('success', 'Absence mise à jour avec succès.');
    }

    /**
     * Supprime une absence
     */
    public function destroy($id)
    {
        $absence = Absence::findOrFail($id);
        $absence->delete();

        return redirect()->route('cpe.absences.index')
            ->with('success', 'Absence supprimée avec succès.');
    }

    /**
     * Justifie une absence
     */
    public function justify(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'justification' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $absence->update([
            'justifiee' => true,
            'motif' => $request->justification,
        ]);

        return redirect()->route('cpe.absences.index')
            ->with('success', 'Absence justifiée avec succès.');
    }

    /**
     * Export des absences
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe']);
        
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', fn($q) => $q->where('classe_id', $request->classe_id));
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $absences = $query->orderBy('date', 'desc')->get();
        
        $filename = 'absences-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // BOM UTF-8 pour Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($handle, ['Date', 'Élève', 'Classe', 'Type', 'Justifiée', 'Motif', 'Heure']);
        
        foreach ($absences as $absence) {
            fputcsv($handle, [
                $absence->date->format('d/m/Y'),
                $absence->eleve->prenom . ' ' . $absence->eleve->nom,
                $absence->eleve->classe->nom,
                $absence->type,
                $absence->justifiee ? 'Oui' : 'Non',
                $absence->motif ?? '',
                $absence->heure ?? '',
            ]);
        }
        
        fclose($handle);
        exit;
    }
}