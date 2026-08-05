<?php
// app/Http/Controllers/Etablissement/MatiereController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class MatiereController extends Controller
{
    /**
     * Affiche la liste des matières
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        // Récupérer les matières
        $query = Matiere::with('classes', 'enseignants.user');
        
        // Si les matières sont liées à l'établissement
        if (Schema::hasColumn('matieres', 'etablissement_id')) {
            $query->where('etablissement_id', $etablissementId);
        }
        
        // Filtre par niveau
        if ($request->filled('niveau')) {
            $query->where('niveau', $request->niveau);
        }
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $matieres = $query->orderBy('nom')->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => Matiere::count(),
            'actives' => Matiere::has('enseignants')->count(),
            'par_niveau' => Matiere::selectRaw('niveau, count(*) as total')
                ->groupBy('niveau')
                ->pluck('total', 'niveau'),
        ];
        
        return view('etablissement.matieres.index', compact('matieres', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        // ✅ CORRECTION : Récupérer les enseignants via la relation user
        $enseignants = Enseignant::whereHas('user', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with('user')
            ->get()
            ->mapWithKeys(function($enseignant) {
                return [$enseignant->id => $enseignant->user->name ?? 'Sans nom'];
            });
        
        $niveaux = [
            'Primaire' => 'Primaire',
            'Collège' => 'Collège',
            'Lycée' => 'Lycée',
            'Tous' => 'Tous les niveaux',
        ];
        
        return view('etablissement.matieres.create', compact('classes', 'enseignants', 'niveaux'));
    }

    /**
     * Enregistre une nouvelle matière
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:matieres,code',
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'niveau' => 'required|in:Primaire,Collège,Lycée,Tous',
            'description' => 'nullable|string|max:500',
            'classe_ids' => 'nullable|array',
            'classe_ids.*' => 'exists:classes,id',
            'enseignant_ids' => 'nullable|array',
            'enseignant_ids.*' => 'exists:enseignants,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['classe_ids', 'enseignant_ids']);
        
        // Ajouter l'établissement si la colonne existe
        if (Schema::hasColumn('matieres', 'etablissement_id')) {
            $data['etablissement_id'] = $user->etablissement_id;
        }

        $matiere = Matiere::create($data);

        // Associer aux classes si nécessaire
        if ($request->has('classe_ids')) {
            $matiere->classes()->sync($request->classe_ids);
        }

        // Associer aux enseignants si nécessaire
        if ($request->has('enseignant_ids')) {
            $matiere->enseignants()->sync($request->enseignant_ids);
        }

        return redirect()->route('etablissement.matieres.index')
            ->with('success', 'Matière créée avec succès.');
    }

    /**
     * Affiche les détails d'une matière
     */
    public function show($id)
    {
        $matiere = Matiere::with(['classes', 'enseignants.user', 'notes' => function($q) {
                $q->latest()->take(10);
            }])
            ->findOrFail($id);
        
        // Statistiques
        $stats = [
            'total_classes' => $matiere->classes->count(),
            'total_enseignants' => $matiere->enseignants->count(),
            'total_notes' => $matiere->notes()->count(),
            'moyenne_notes' => round($matiere->notes()->avg('valeur') ?? 0, 2),
            'coefficient' => $matiere->coefficient,
        ];
        
        return view('etablissement.matieres.show', compact('matiere', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $matiere = Matiere::with(['classes', 'enseignants'])->findOrFail($id);
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        // ✅ CORRECTION : Récupérer les enseignants via la relation user
        $enseignants = Enseignant::whereHas('user', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with('user')
            ->get()
            ->mapWithKeys(function($enseignant) {
                return [$enseignant->id => $enseignant->user->name ?? 'Sans nom'];
            });
        
        $niveaux = [
            'Primaire' => 'Primaire',
            'Collège' => 'Collège',
            'Lycée' => 'Lycée',
            'Tous' => 'Tous les niveaux',
        ];
        
        return view('etablissement.matieres.edit', compact('matiere', 'classes', 'enseignants', 'niveaux'));
    }

    /**
     * Met à jour une matière
     */
    public function update(Request $request, $id)
    {
        $matiere = Matiere::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:matieres,code,' . $id,
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'niveau' => 'required|in:Primaire,Collège,Lycée,Tous',
            'description' => 'nullable|string|max:500',
            'classe_ids' => 'nullable|array',
            'classe_ids.*' => 'exists:classes,id',
            'enseignant_ids' => 'nullable|array',
            'enseignant_ids.*' => 'exists:enseignants,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $matiere->update($request->except(['classe_ids', 'enseignant_ids']));

        // Mettre à jour les associations avec les classes
        if ($request->has('classe_ids')) {
            $matiere->classes()->sync($request->classe_ids);
        } else {
            $matiere->classes()->detach();
        }

        // Mettre à jour les associations avec les enseignants
        if ($request->has('enseignant_ids')) {
            $matiere->enseignants()->sync($request->enseignant_ids);
        } else {
            $matiere->enseignants()->detach();
        }

        return redirect()->route('etablissement.matieres.index')
            ->with('success', 'Matière mise à jour avec succès.');
    }

    /**
     * Supprime une matière
     */
    public function destroy($id)
    {
        $matiere = Matiere::findOrFail($id);
        
        // Vérifier si la matière est utilisée
        if ($matiere->notes()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette matière car elle est utilisée dans des notes.');
        }
        
        if ($matiere->emploisTemps()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette matière car elle est utilisée dans des emplois du temps.');
        }
        
        // Supprimer les associations
        $matiere->classes()->detach();
        $matiere->enseignants()->detach();
        
        $matiere->delete();

        return redirect()->route('etablissement.matieres.index')
            ->with('success', 'Matière supprimée avec succès.');
    }

    /**
     * Exporte la liste des matières
     */
    public function export()
    {
        $user = auth()->user();
        
        $query = Matiere::with(['classes', 'enseignants.user']);
        
        if (Schema::hasColumn('matieres', 'etablissement_id')) {
            $query->where('etablissement_id', $user->etablissement_id);
        }
        
        $matieres = $query->orderBy('nom')->get();
        
        $filename = 'matieres-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, [
            'Nom', 
            'Code', 
            'Coefficient', 
            'Niveau', 
            'Description', 
            'Classes', 
            'Enseignants'
        ]);
        
        foreach ($matieres as $matiere) {
            $classes = $matiere->classes->pluck('nom')->implode(', ');
            $enseignants = $matiere->enseignants->map(function($e) {
                return $e->user->name ?? 'Sans nom';
            })->implode(', ');
            
            fputcsv($handle, [
                $matiere->nom,
                $matiere->code,
                $matiere->coefficient,
                $matiere->niveau,
                $matiere->description ?? '',
                $classes ?: 'Aucune',
                $enseignants ?: 'Aucun',
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Recherche de matières (pour AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $user = auth()->user();
        
        $matieresQuery = Matiere::where('nom', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%");
        
        if (Schema::hasColumn('matieres', 'etablissement_id')) {
            $matieresQuery->where('etablissement_id', $user->etablissement_id);
        }
        
        $matieres = $matieresQuery->limit(10)
            ->get(['id', 'nom', 'code', 'coefficient', 'niveau']);
        
        return response()->json($matieres);
    }

    /**
     * Statistiques des matières (API)
     */
    public function statistiques()
    {
        $user = auth()->user();
        
        $query = Matiere::withCount(['notes', 'emploisTemps', 'enseignants', 'classes']);
        
        if (Schema::hasColumn('matieres', 'etablissement_id')) {
            $query->where('etablissement_id', $user->etablissement_id);
        }
        
        $matieres = $query->get();
        
        $stats = [
            'total' => $matieres->count(),
            'total_coefficient' => $matieres->sum('coefficient'),
            'moyenne_coefficient' => round($matieres->avg('coefficient'), 2),
            'par_niveau' => $matieres->groupBy('niveau')->map->count(),
            'plus_utilisees' => $matieres->sortByDesc('notes_count')->take(5)->values(),
            'moins_utilisees' => $matieres->sortBy('notes_count')->take(5)->values(),
            'top_enseignants' => $matieres->sortByDesc('enseignants_count')->take(5)->values(),
            'top_classes' => $matieres->sortByDesc('classes_count')->take(5)->values(),
        ];
        
        return response()->json($stats);
    }
}