<?php
// app/Http/Controllers/Etablissement/EnseignantController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\EmploiTemps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EmploiTempsController extends Controller
{
    /**
     * Affiche la liste des enseignants
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        // Récupérer tous les enseignants de l'établissement avec leurs relations
        $enseignants = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', function($q) {
                $q->where('name', 'enseignant');
            })
            ->with(['enseignant.matieres', 'classesEnseignees', 'classes'])
            ->orderBy('name')
            ->paginate(15);
        
        // Statistiques détaillées pour la vue
        $total = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->count();
        
        $actifs = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->where('is_active', true)
            ->count();
        
        // Enseignants qui sont professeurs principaux (ont des classes via la relation 'classes')
        $avecClassesPP = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->whereHas('classes')
            ->count();
        
        // Récupérer les spécialités via les utilisateurs
        $enseignantsActifs = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->where('is_active', true)
            ->with('enseignant')
            ->get();
        
        $specialitesList = [];
        foreach ($enseignantsActifs as $ens) {
            if ($ens->enseignant && $ens->enseignant->specialite) {
                $specialitesList[] = $ens->enseignant->specialite;
            }
        }
        $specialitesCount = count(array_unique($specialitesList));
        
        // Liste des spécialités pour le filtre
        $specialites = array_unique($specialitesList);
        
        // Ajouter les spécialités par défaut si aucune n'est trouvée
        if (empty($specialites)) {
            $specialites = [
                'Mathématiques',
                'Physique-Chimie',
                'SVT',
                'Français',
                'Anglais',
                'Histoire-Géographie',
                'Philosophie',
                'EPS',
                'Informatique',
                'Arabe',
                'Espagnol',
                'Allemand',
            ];
        }
        
        $stats = [
            'total' => $total,
            'actifs' => $actifs,
            'inactifs' => $total - $actifs,
            'avec_classes_pp' => $avecClassesPP,
            'sans_classe_pp' => $actifs - $avecClassesPP,
            'specialites' => $specialitesCount,
        ];
        
        return view('etablissement.enseignants.index', compact('enseignants', 'stats', 'specialites'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        // Filtrer les classes par établissement
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        $matieres = Matiere::where('etablissement_id', $etablissementId)->get();
        
        $specialites = [
            'Mathématiques',
            'Physique-Chimie',
            'SVT',
            'Français',
            'Anglais',
            'Histoire-Géographie',
            'Philosophie',
            'EPS',
            'Informatique',
            'Arabe',
            'Espagnol',
            'Allemand',
        ];
        
        return view('etablissement.enseignants.create', compact('classes', 'matieres', 'specialites'));
    }

    /**
     * Enregistre un nouvel enseignant (SÉCURISÉ)
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'matricule' => 'required|string|unique:enseignants,matricule|max:20',
            'specialite' => 'required|string|max:100',
            'date_embauche' => 'required|date',
            'adresse' => 'nullable|string|max:255',
            'classes' => 'nullable|array', // Classes comme professeur principal
            'classes.*' => 'exists:classes,id',
            'classes_enseignees' => 'nullable|array', // Classes où il enseigne
            'classes_enseignees.*' => 'exists:classes,id',
            'matieres' => 'nullable|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // VÉRIFICATION CRUCIALE : S'assurer que toutes les classes appartiennent à l'établissement
        $classesInvalides = [];
        
        if ($request->has('classes')) {
            foreach ($request->classes as $classeId) {
                $classe = Classe::find($classeId);
                if (!$classe || $classe->etablissement_id != $etablissementId) {
                    $classesInvalides[] = $classeId;
                }
            }
        }
        
        if ($request->has('classes_enseignees')) {
            foreach ($request->classes_enseignees as $classeId) {
                $classe = Classe::find($classeId);
                if (!$classe || $classe->etablissement_id != $etablissementId) {
                    $classesInvalides[] = $classeId;
                }
            }
        }

        // Si des classes invalides sont trouvées, on bloque et on affiche une erreur
        if (!empty($classesInvalides)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'classes' => 'Tentative d\'affectation à des classes qui ne font pas partie de votre établissement. Opération bloquée pour des raisons de sécurité.'
                ]);
        }

        // VÉRIFICATION : S'assurer que les matières existent et appartiennent à l'établissement
        if ($request->has('matieres')) {
            $matieresValides = Matiere::whereIn('id', $request->matieres)
                ->where('etablissement_id', $etablissementId)
                ->count();
            if ($matieresValides != count($request->matieres)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'matieres' => 'Certaines matières sélectionnées n\'existent pas ou ne sont pas dans votre établissement.'
                    ]);
            }
        }

        // Créer l'utilisateur avec l'établissement de l'admin
        $enseignant = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3, // ID du rôle enseignant
            'etablissement_id' => $etablissementId,
            'telephone' => $request->telephone,
            'is_active' => true,
        ]);

        // Créer le profil enseignant
        $profil = Enseignant::create([
            'user_id' => $enseignant->id,
            'matricule' => $request->matricule,
            'specialite' => $request->specialite,
            'date_embauche' => $request->date_embauche,
            'adresse' => $request->adresse,
        ]);

        // Gestion des classes (professeur principal)
        if ($request->has('classes')) {
            Classe::where('professeur_principal_id', $enseignant->id)
                  ->update(['professeur_principal_id' => null]);
            
            Classe::whereIn('id', $request->classes)
                  ->update(['professeur_principal_id' => $enseignant->id]);
        }

        // Gestion des classes enseignées (Many-to-Many)
        if ($request->has('classes_enseignees')) {
            $enseignant->classesEnseignees()->sync($request->classes_enseignees);
        }

        // Gestion des matières (Many-to-Many)
        if ($request->has('matieres')) {
            $profil->matieres()->sync($request->matieres);
        }

        // Journaliser l'action
        activity()
            ->causedBy(auth()->user())
            ->performedOn($enseignant)
            ->withProperties([
                'name' => $enseignant->name,
                'email' => $enseignant->email
            ])
            ->event('create')
            ->log('Enseignant créé');

        return redirect()->route('etablissement.enseignants.index')
            ->with('success', 'Enseignant créé avec succès et rattaché à votre établissement.');
    }

    /**
     * Affiche les détails d'un enseignant
     */
    public function show($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $enseignant = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->with([
                'enseignant.matieres', 
                'classesEnseignees', 
                'classes',
                'emploisTemps' => function($q) {
                    $q->with(['matiere', 'classe']);
                }
            ])
            ->findOrFail($id);
        
        // Statistiques pour l'affichage
        $stats = [
            'classes_principales' => $enseignant->classes->count(),
            'heures_cours' => $this->calculerHeuresCours($enseignant),
            'matieres_enseignees' => $enseignant->enseignant->matieres->count(),
            'nb_eleves' => $this->calculerNombreEleves($enseignant),
        ];
        
        return view('etablissement.enseignants.show', compact('enseignant', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $enseignant = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->with(['enseignant', 'classesEnseignees'])
            ->findOrFail($id);
        
        // Classes disponibles dans l'établissement
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        // Matières disponibles dans l'établissement
        $matieres = Matiere::where('etablissement_id', $etablissementId)->get();
        
        $specialites = [
            'Mathématiques',
            'Physique-Chimie',
            'SVT',
            'Français',
            'Anglais',
            'Histoire-Géographie',
            'Philosophie',
            'EPS',
            'Informatique',
            'Arabe',
            'Espagnol',
            'Allemand',
        ];
        
        return view('etablissement.enseignants.edit', compact('enseignant', 'classes', 'matieres', 'specialites'));
    }

    /**
     * Met à jour un enseignant (SÉCURISÉ)
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $enseignant = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'matricule' => 'required|string|unique:enseignants,matricule,' . $enseignant->enseignant->id . '|max:20',
            'specialite' => 'required|string|max:100',
            'date_embauche' => 'required|date',
            'adresse' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id',
            'classes_enseignees' => 'nullable|array',
            'classes_enseignees.*' => 'exists:classes,id',
            'matieres' => 'nullable|array',
            'matieres.*' => 'exists:matieres,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // VÉRIFICATION : Les classes doivent appartenir à l'établissement
        $classesInvalides = [];
        
        $classesIds = array_merge($request->classes ?? [], $request->classes_enseignees ?? []);
        foreach ($classesIds as $classeId) {
            $classe = Classe::find($classeId);
            if (!$classe || $classe->etablissement_id != $etablissementId) {
                $classesInvalides[] = $classeId;
            }
        }

        if (!empty($classesInvalides)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'classes' => 'Tentative d\'affectation à des classes hors de votre établissement.'
                ]);
        }

        // Vérification des matières
        if ($request->has('matieres')) {
            $matieresValides = Matiere::whereIn('id', $request->matieres)
                ->where('etablissement_id', $etablissementId)
                ->count();
            if ($matieresValides != count($request->matieres)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'matieres' => 'Certaines matières sélectionnées ne sont pas dans votre établissement.'
                    ]);
            }
        }

        // Mettre à jour l'utilisateur (l'établissement ne change pas !)
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $enseignant->update($data);

        // Mettre à jour le profil enseignant
        $enseignant->enseignant->update([
            'matricule' => $request->matricule,
            'specialite' => $request->specialite,
            'date_embauche' => $request->date_embauche,
            'adresse' => $request->adresse,
        ]);

        // Mise à jour des classes (professeur principal)
        Classe::where('professeur_principal_id', $enseignant->id)
              ->update(['professeur_principal_id' => null]);
        
        if ($request->has('classes')) {
            Classe::whereIn('id', $request->classes)
                  ->update(['professeur_principal_id' => $enseignant->id]);
        }

        // Mise à jour des classes enseignées
        $enseignant->classesEnseignees()->sync($request->classes_enseignees ?? []);

        // Mise à jour des matières
        $enseignant->enseignant->matieres()->sync($request->matieres ?? []);

        // Journaliser l'action
        activity()
            ->causedBy(auth()->user())
            ->performedOn($enseignant)
            ->withProperties([
                'name' => $enseignant->name,
                'email' => $enseignant->email,
                'is_active' => $enseignant->is_active
            ])
            ->event('update')
            ->log('Enseignant modifié');

        return redirect()->route('etablissement.enseignants.index')
            ->with('success', 'Enseignant mis à jour avec succès.');
    }

    /**
     * Supprime un enseignant (désactive)
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $enseignant = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->findOrFail($id);
        
        // Désactiver plutôt que supprimer
        $enseignant->update(['is_active' => false]);
        
        // Enlever des classes (professeur principal)
        Classe::where('professeur_principal_id', $enseignant->id)
              ->update(['professeur_principal_id' => null]);
        
        // Journaliser
        activity()
            ->causedBy(auth()->user())
            ->performedOn($enseignant)
            ->withProperties(['name' => $enseignant->name])
            ->event('delete')
            ->log('Enseignant désactivé');
        
        return redirect()->route('etablissement.enseignants.index')
            ->with('success', 'Enseignant désactivé avec succès.');
    }

    /**
     * Affiche l'emploi du temps d'un enseignant
     */
    public function emploiTemps($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $enseignant = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->with(['enseignant.matieres'])
            ->findOrFail($id);
        
        // ✅ CORRECTION : Récupérer l'emploi du temps avec 'jour' au lieu de 'jour_semaine'
        $emploisTemps = EmploiTemps::where('enseignant_id', $id)
            ->with(['classe', 'matiere'])
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('jour');
        
        // Ordre des jours pour l'affichage
        $ordreJours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        
        // Statistiques
        $stats = [
            'total_cours' => EmploiTemps::where('enseignant_id', $id)->count(),
            'matieres_enseignees' => $enseignant->enseignant->matieres->count(),
            'classes' => EmploiTemps::where('enseignant_id', $id)->distinct('classe_id')->count('classe_id'),
            'heures_semaine' => $this->calculerHeuresSemaine($id),
        ];
        
        return view('etablissement.enseignants.emploi-temps', compact('enseignant', 'emploisTemps', 'ordreJours', 'stats'));
    }

    /**
     * Exporte la liste des enseignants
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $format = $request->get('format', 'csv');
        
        $enseignants = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->with(['enseignant', 'classes'])
            ->orderBy('name')
            ->get();
        
        if ($format === 'csv') {
            $filename = 'enseignants_' . date('Y-m-d') . '.csv';
            $handle = fopen('php://output', 'w');
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            // En-têtes CSV
            fputcsv($handle, ['Nom', 'Email', 'Téléphone', 'Matricule', 'Spécialité', 'Date embauche', 'Statut', 'Classes principales']);
            
            foreach ($enseignants as $enseignant) {
                $classesPrincipales = $enseignant->classes->pluck('nom')->implode(', ');
                
                fputcsv($handle, [
                    $enseignant->name,
                    $enseignant->email,
                    $enseignant->telephone ?? 'N/A',
                    $enseignant->enseignant->matricule ?? 'N/A',
                    $enseignant->enseignant->specialite ?? 'N/A',
                    $enseignant->enseignant->date_embauche ? $enseignant->enseignant->date_embauche->format('d/m/Y') : 'N/A',
                    $enseignant->is_active ? 'Actif' : 'Inactif',
                    $classesPrincipales ?: 'Aucune'
                ]);
            }
            
            fclose($handle);
            exit;
        }
        
        return redirect()->back()->with('error', 'Format non supporté');
    }

    /**
     * Recherche d'enseignants (pour AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $enseignants = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhereHas('enseignant', function($q2) use ($query) {
                      $q2->where('matricule', 'like', "%{$query}%")
                         ->orWhere('specialite', 'like', "%{$query}%");
                  });
            })
            ->with('enseignant')
            ->limit(10)
            ->get(['id', 'name', 'email']);
        
        return response()->json($enseignants);
    }

    /**
     * Statistiques des enseignants
     */
    public function statistiques()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $total = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->count();
        
        $actifs = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->where('is_active', true)
            ->count();
        
        $stats = [
            'total' => $total,
            'actifs' => $actifs,
            'inactifs' => $total - $actifs,
            'par_specialite' => Enseignant::whereHas('user', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->selectRaw('specialite, count(*) as total')
                ->groupBy('specialite')
                ->pluck('total', 'specialite'),
            'professeurs_principaux' => User::where('etablissement_id', $etablissementId)
                ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
                ->whereHas('classes')
                ->count(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Calcule le nombre d'heures de cours par semaine
     */
    private function calculerHeuresCours($enseignant)
    {
        if (!$enseignant->emploisTemps || $enseignant->emploisTemps->isEmpty()) {
            return 0;
        }
        
        $totalMinutes = 0;
        foreach ($enseignant->emploisTemps as $cours) {
            if ($cours->heure_debut && $cours->heure_fin) {
                $debut = Carbon::parse($cours->heure_debut);
                $fin = Carbon::parse($cours->heure_fin);
                $totalMinutes += $debut->diffInMinutes($fin);
            }
        }
        
        return round($totalMinutes / 60, 1);
    }

    /**
     * Calcule le nombre total d'élèves que l'enseignant a en charge
     */
    private function calculerNombreEleves($enseignant)
    {
        $totalEleves = 0;
        $classeIds = [];
        
        // Élèves dans ses classes (professeur principal)
        foreach ($enseignant->classes as $classe) {
            $classeIds[] = $classe->id;
            $totalEleves += $classe->eleves()->count();
        }
        
        // Élèves dans les classes où il enseigne (via emplois du temps)
        if ($enseignant->emploisTemps) {
            foreach ($enseignant->emploisTemps as $cours) {
                if ($cours->classe_id && !in_array($cours->classe_id, $classeIds)) {
                    $classeIds[] = $cours->classe_id;
                    if ($classe = Classe::find($cours->classe_id)) {
                        $totalEleves += $classe->eleves()->count();
                    }
                }
            }
        }
        
        return $totalEleves;
    }

    /**
     * Calcule le nombre d'heures de cours par semaine pour un enseignant
     */
    private function calculerHeuresSemaine($enseignantId)
    {
        $emplois = EmploiTemps::where('enseignant_id', $enseignantId)->get();
        $totalMinutes = 0;
        
        foreach ($emplois as $emploi) {
            $debut = Carbon::parse($emploi->heure_debut);
            $fin = Carbon::parse($emploi->heure_fin);
            $totalMinutes += $debut->diffInMinutes($fin);
        }
        
        return round($totalMinutes / 60, 1);
    }
}