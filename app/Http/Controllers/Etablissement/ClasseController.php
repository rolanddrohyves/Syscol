<?php
// app/Http/Controllers/Etablissement/ClasseController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Etablissement;
use App\Models\User;
use App\Models\AnneeScolaire;
use App\Models\Eleve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClasseController extends Controller
{
    /**
     * Affiche la liste des classes
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Classe::with(['etablissement', 'professeurPrincipal', 'anneeScolaire'])
            ->withCount('eleves')
            ->where('etablissement_id', $etablissementId);
        
        // Filtres
        if ($request->filled('niveau')) {
            $query->where('niveau', $request->niveau);
        }
        
        if ($request->filled('annee_scolaire_id')) {
            $query->where('annee_scolaire_id', $request->annee_scolaire_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('serie', 'like', "%{$search}%");
            });
        }
        
        $classes = $query->orderBy('niveau')
            ->orderBy('nom')
            ->paginate(15);
        
        // Données pour les filtres
        $niveaux = Classe::where('etablissement_id', $etablissementId)
            ->distinct()
            ->pluck('niveau');
        
        $anneesScolaires = AnneeScolaire::orderBy('libelle', 'desc')->get();
        
        // Statistiques
        $stats = [
            'total' => Classe::where('etablissement_id', $etablissementId)->count(),
            'total_eleves' => Eleve::whereIn('classe_id', 
                Classe::where('etablissement_id', $etablissementId)->pluck('id')
            )->count(),
            'capacite_totale' => Classe::where('etablissement_id', $etablissementId)->sum('capacite'),
            'avec_pp' => Classe::where('etablissement_id', $etablissementId)
                ->whereNotNull('professeur_principal_id')
                ->count(),
        ];
        
        return view('etablissement.classes.index', compact('classes', 'niveaux', 'anneesScolaires', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $anneesScolaires = AnneeScolaire::orderBy('libelle', 'desc')->get();
        $professeurs = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->orderBy('name')
            ->get();
        
        $niveaux = [
            'Primaire' => ['CI', 'CP', 'CE1', 'CE2', 'CM1', 'CM2'],
            'Collège' => ['6ème', '5ème', '4ème', '3ème'],
            'Lycée' => ['Seconde', 'Première', 'Terminale'],
        ];
        
        $series = ['L', 'S', 'SE', 'STEG', 'STT'];
        
        return view('etablissement.classes.create', compact('anneesScolaires', 'professeurs', 'niveaux', 'series'));
    }

    /**
     * Enregistre une nouvelle classe
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:50',
            'niveau' => 'required|in:Primaire,Collège,Lycée',
            'serie' => 'nullable|string|max:10',
            'capacite' => 'required|integer|min:10|max:60',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'professeur_principal_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que le professeur principal appartient bien à l'établissement
        if ($request->filled('professeur_principal_id')) {
            $prof = User::find($request->professeur_principal_id);
            if ($prof->etablissement_id != $user->etablissement_id) {
                return redirect()->back()
                    ->with('error', 'Le professeur principal doit appartenir à votre établissement.')
                    ->withInput();
            }
        }

        // Vérifier l'unicité du nom de classe pour l'année en cours
        $exists = Classe::where('etablissement_id', $user->etablissement_id)
            ->where('nom', $request->nom)
            ->where('annee_scolaire_id', $request->annee_scolaire_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Une classe avec ce nom existe déjà pour cette année scolaire.')
                ->withInput();
        }

        Classe::create([
            'etablissement_id' => $user->etablissement_id,
            'nom' => $request->nom,
            'niveau' => $request->niveau,
            'serie' => $request->serie,
            'capacite' => $request->capacite,
            'annee_scolaire_id' => $request->annee_scolaire_id,
            'professeur_principal_id' => $request->professeur_principal_id,
        ]);

        return redirect()->route('etablissement.classes.index')
            ->with('success', 'Classe créée avec succès.');
    }

    /**
     * Affiche les détails d'une classe
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $classe = Classe::with(['etablissement', 'professeurPrincipal', 'anneeScolaire'])
            ->withCount('eleves')
            ->findOrFail($id);
        
        // Vérifier que la classe appartient à l'établissement de l'utilisateur
        if ($classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cette classe.');
        }
        
        $eleves = Eleve::where('classe_id', $id)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(20);
        
        $stats = [
            'total_eleves' => $classe->eleves_count,
            'places_disponibles' => $classe->capacite - $classe->eleves_count,
            'taux_occupation' => $classe->capacite > 0 
                ? round(($classe->eleves_count / $classe->capacite) * 100, 1) 
                : 0,
            'filles' => Eleve::where('classe_id', $id)->where('sexe', 'F')->count(),
            'garcons' => Eleve::where('classe_id', $id)->where('sexe', 'M')->count(),
        ];
        
        return view('etablissement.classes.show', compact('classe', 'eleves', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        // Charger la classe avec le comptage des élèves
        $classe = Classe::withCount('eleves')
            ->with('etablissement')
            ->findOrFail($id);
        
        // Vérifier que la classe appartient à l'établissement de l'utilisateur
        if ($classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cette classe.');
        }
        
        $anneesScolaires = AnneeScolaire::orderBy('libelle', 'desc')->get();
        $professeurs = User::where('etablissement_id', $user->etablissement_id)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->orderBy('name')
            ->get();
        
        // Récupérer les valeurs uniques pour les select
        $niveaux = Classe::where('etablissement_id', $user->etablissement_id)
            ->distinct()
            ->pluck('niveau')
            ->toArray();
        
        $series = ['L', 'S', 'SE', 'STEG', 'STT'];
        
        // Ajouter l'effectif à la classe pour la vue
        $classe->effectif_actuel = $classe->eleves_count;
        
        return view('etablissement.classes.edit', compact('classe', 'anneesScolaires', 'professeurs', 'niveaux', 'series'));
    }

    /**
     * Met à jour une classe
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $classe = Classe::findOrFail($id);
        
        // Vérifier que la classe appartient à l'établissement de l'utilisateur
        if ($classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cette classe.');
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:50',
            'niveau' => 'required|in:Primaire,Collège,Lycée',
            'serie' => 'nullable|string|max:10',
            'capacite' => 'required|integer|min:10|max:60',
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'professeur_principal_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que le professeur principal appartient bien à l'établissement
        if ($request->filled('professeur_principal_id')) {
            $prof = User::find($request->professeur_principal_id);
            if ($prof->etablissement_id != $user->etablissement_id) {
                return redirect()->back()
                    ->with('error', 'Le professeur principal doit appartenir à votre établissement.')
                    ->withInput();
            }
        }

        // Vérifier l'unicité du nom de classe (sauf pour cette classe)
        $exists = Classe::where('etablissement_id', $user->etablissement_id)
            ->where('nom', $request->nom)
            ->where('annee_scolaire_id', $request->annee_scolaire_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Une classe avec ce nom existe déjà pour cette année scolaire.')
                ->withInput();
        }

        // Vérifier que la capacité n'est pas inférieure à l'effectif actuel
        if ($request->capacite < $classe->eleves()->count()) {
            return redirect()->back()
                ->with('error', 'La capacité ne peut pas être inférieure au nombre d\'élèves actuels (' . $classe->eleves()->count() . ').')
                ->withInput();
        }

        $classe->update([
            'nom' => $request->nom,
            'niveau' => $request->niveau,
            'serie' => $request->serie,
            'capacite' => $request->capacite,
            'annee_scolaire_id' => $request->annee_scolaire_id,
            'professeur_principal_id' => $request->professeur_principal_id,
        ]);

        return redirect()->route('etablissement.classes.index')
            ->with('success', 'Classe mise à jour avec succès.');
    }

    /**
     * Supprime une classe
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $classe = Classe::findOrFail($id);
        
        // Vérifier que la classe appartient à l'établissement de l'utilisateur
        if ($classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cette classe.');
        }

        // Vérifier si la classe a des élèves
        if ($classe->eleves()->count() > 0) {
            return redirect()->route('etablissement.classes.index')
                ->with('error', 'Impossible de supprimer une classe qui contient des élèves.');
        }

        $classe->delete();

        return redirect()->route('etablissement.classes.index')
            ->with('success', 'Classe supprimée avec succès.');
    }

    /**
     * Liste des élèves d'une classe (AVEC STATISTIQUES)
     */
    public function eleves(Request $request, $id)
    {
        $user = auth()->user();
        $classe = Classe::withCount('eleves')->findOrFail($id);
        
        // Vérifier que la classe appartient à l'établissement de l'utilisateur
        if ($classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cette classe.');
        }
        
        // Requête de base pour les élèves
        $query = Eleve::where('classe_id', $id);
        
        // Filtre par sexe
        if ($request->filled('sexe')) {
            $query->where('sexe', $request->sexe);
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }
        
        // Pagination
        $eleves = $query->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(15)
            ->withQueryString();
        
        // Statistiques pour la classe
        $stats = [
            'total' => $classe->eleves_count,
            'filles' => Eleve::where('classe_id', $id)->where('sexe', 'F')->count(),
            'garcons' => Eleve::where('classe_id', $id)->where('sexe', 'M')->count(),
            'taux_occupation' => $classe->capacite > 0 
                ? round(($classe->eleves_count / $classe->capacite) * 100, 1) 
                : 0,
            'actifs' => Eleve::where('classe_id', $id)->where('status', 'actif')->count(),
            'exclus' => Eleve::where('classe_id', $id)->where('status', 'exclu')->count(),
        ];
        
        return view('etablissement.classes.eleves', compact('classe', 'eleves', 'stats'));
    }

    /**
     * Export de la liste des classes
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'csv');
        
        $classes = Classe::withCount('eleves')
            ->with(['professeurPrincipal', 'anneeScolaire'])
            ->where('etablissement_id', $user->etablissement_id)
            ->get();
        
        switch ($format) {
            case 'csv':
                return $this->exportCsv($classes);
            default:
                return redirect()->back()->with('error', 'Format non supporté');
        }
    }

    /**
     * Export CSV
     */
    private function exportCsv($classes)
    {
        $filename = 'classes-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['Classe', 'Niveau', 'Série', 'Capacité', 'Effectif', 'Professeur principal', 'Année scolaire']);
        
        foreach ($classes as $classe) {
            fputcsv($handle, [
                $classe->nom,
                $classe->niveau,
                $classe->serie ?? '-',
                $classe->capacite,
                $classe->eleves_count,
                $classe->professeurPrincipal->name ?? 'Non assigné',
                $classe->anneeScolaire->libelle,
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Graphique de répartition
     */
    public function chartData()
    {
        $user = auth()->user();
        
        $data = Classe::where('etablissement_id', $user->etablissement_id)
            ->withCount('eleves')
            ->get()
            ->map(fn($classe) => [
                'classe' => $classe->nom,
                'effectif' => $classe->eleves_count,
                'capacite' => $classe->capacite,
            ]);
        
        return response()->json($data);
    }
}