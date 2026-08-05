<?php
// app/Http/Controllers/Cpe/DisciplineController.php

namespace App\Http\Controllers\Cpe;

use App\Http\Controllers\Controller;
use App\Models\Discipline;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DisciplineController extends Controller
{
    /**
     * Affiche la liste des incidents disciplinaires
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Discipline::whereHas('eleve.classe', function($q) use ($etablissementId) {
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
        
        if ($request->filled('gravite')) {
            $query->where('gravite', $request->gravite);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $disciplines = $query->orderBy('date', 'desc')->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        // Statistiques
        $stats = [
            'total' => Discipline::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count(),
            'cette_semaine' => Discipline::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'ce_mois' => Discipline::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->whereMonth('date', Carbon::now()->month)
                ->count(),
            'eleves_distincts' => Discipline::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->distinct('eleve_id')
                ->count('eleve_id'),
        ];
        
        // Élèves les plus problématiques
        $elevesProblematiques = Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
            ->with('classe')
            ->withCount('disciplines')
            ->orderBy('disciplines_count', 'desc')
            ->limit(6)
            ->get();
        
        return view('cpe.disciplines.index', compact('disciplines', 'classes', 'stats', 'elevesProblematiques'));
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
        
        return view('cpe.disciplines.create', compact('classes', 'elevesParClasse', 'eleveId', 'classeId'));
    }

    /**
     * Enregistre un nouvel incident
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'type' => 'required|string',
            'gravite' => 'required|in:faible,moyenne,elevee,critique',
            'date' => 'required|date',
            'heure' => 'nullable|date_format:H:i',
            'description' => 'required|string|max:500',
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

        Discipline::create([
            'eleve_id' => $request->eleve_id,
            'type' => $request->type,
            'gravite' => $request->gravite,
            'date' => $request->date,
            'heure' => $request->heure,
            'description' => $request->description,
        ]);

        return redirect()->route('cpe.disciplines.index')
            ->with('success', 'Incident enregistré avec succès.');
    }

    /**
     * Affiche les détails d'un incident
     */
    public function show($id)
    {
        $discipline = Discipline::with(['eleve', 'eleve.classe'])->findOrFail($id);
        
        return view('cpe.disciplines.show', compact('discipline'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $discipline = Discipline::findOrFail($id);
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
        
        return view('cpe.disciplines.edit', compact('discipline', 'classes', 'elevesParClasse'));
    }

    /**
     * Met à jour un incident
     */
    public function update(Request $request, $id)
    {
        $discipline = Discipline::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'classe_id' => 'required|exists:classes,id',
            'type' => 'required|string',
            'gravite' => 'required|in:faible,moyenne,elevee,critique',
            'date' => 'required|date',
            'heure' => 'nullable|date_format:H:i',
            'description' => 'required|string|max:500',
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

        $discipline->update([
            'eleve_id' => $request->eleve_id,
            'type' => $request->type,
            'gravite' => $request->gravite,
            'date' => $request->date,
            'heure' => $request->heure,
            'description' => $request->description,
        ]);

        return redirect()->route('cpe.disciplines.index')
            ->with('success', 'Incident mis à jour avec succès.');
    }

    /**
     * Supprime un incident
     */
    public function destroy($id)
    {
        $discipline = Discipline::findOrFail($id);
        $discipline->delete();

        return redirect()->route('cpe.disciplines.index')
            ->with('success', 'Incident supprimé avec succès.');
    }

    /**
     * Export des incidents au format CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Discipline::whereHas('eleve.classe', function($q) use ($etablissementId) {
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
        
        if ($request->filled('gravite')) {
            $query->where('gravite', $request->gravite);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $disciplines = $query->orderBy('date', 'desc')->get();
        
        $filename = 'disciplines_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($handle, ['Date', 'Heure', 'Élève', 'Classe', 'Type', 'Gravité', 'Description']);
        
        foreach ($disciplines as $discipline) {
            fputcsv($handle, [
                $discipline->date->format('d/m/Y'),
                $discipline->heure ?? '',
                $discipline->eleve->prenom . ' ' . $discipline->eleve->nom,
                $discipline->eleve->classe->nom,
                ucfirst($discipline->type),
                ucfirst($discipline->gravite),
                $discipline->description,
            ]);
        }
        
        fclose($handle);
        exit;
    }
}