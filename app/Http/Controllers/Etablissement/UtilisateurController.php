<?php
// app/Http/Controllers/Etablissement/UtilisateurController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UtilisateurController extends Controller
{
    /**
     * Affiche la liste des utilisateurs (personnel) de l'établissement
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = User::where('etablissement_id', $etablissementId)
            ->with('role')
            ->where('id', '!=', $user->id); // Exclure l'utilisateur connecté
        
        // Filtres
        if ($request->filled('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'actif');
        }
        
        $utilisateurs = $query->orderBy('name')->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => User::where('etablissement_id', $etablissementId)->count(),
            'actifs' => User::where('etablissement_id', $etablissementId)->where('is_active', true)->count(),
            'inactifs' => User::where('etablissement_id', $etablissementId)->where('is_active', false)->count(),
            'admins' => User::where('etablissement_id', $etablissementId)
                ->whereHas('role', fn($q) => $q->where('name', 'admin_etablissement'))
                ->count(),
        ];
        
        $roles = Role::whereIn('name', [
            'admin_etablissement', 
            'directeur_etudes', 
            'cpe', 
            'comptable', 
            'enseignant'
        ])->get();
        
        return view('etablissement.utilisateurs.index', compact('utilisateurs', 'stats', 'roles'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $roles = Role::whereIn('name', [
            'admin_etablissement', 
            'directeur_etudes', 
            'cpe', 
            'comptable', 
            'enseignant'
        ])->get();
        
        return view('etablissement.utilisateurs.create', compact('roles'));
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'telephone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que le rôle est autorisé
        $role = Role::find($request->role_id);
        $rolesAutorises = ['admin_etablissement', 'directeur_etudes', 'cpe', 'comptable', 'enseignant'];
        
        if (!in_array($role->name, $rolesAutorises)) {
            return redirect()->back()
                ->with('error', 'Rôle non autorisé pour un utilisateur d\'établissement.')
                ->withInput();
        }

        $utilisateur = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'etablissement_id' => $etablissementId,
            'telephone' => $request->telephone,
            'is_active' => true,
        ]);

        // Si c'est un enseignant, créer automatiquement le profil enseignant
        if ($role->name === 'enseignant') {
            \App\Models\Enseignant::create([
                'user_id' => $utilisateur->id,
                'matricule' => 'ENS' . str_pad($utilisateur->id, 5, '0', STR_PAD_LEFT),
                'specialite' => 'Non définie',
                'date_embauche' => now(),
            ]);
        }

        return redirect()->route('etablissement.utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $utilisateur = User::where('etablissement_id', $etablissementId)
            ->with(['role', 'enseignant'])
            ->findOrFail($id);
        
        return view('etablissement.utilisateurs.show', compact('utilisateur'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $utilisateur = User::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        $roles = Role::whereIn('name', [
            'admin_etablissement', 
            'directeur_etudes', 
            'cpe', 
            'comptable', 
            'enseignant'
        ])->get();
        
        return view('etablissement.utilisateurs.edit', compact('utilisateur', 'roles'));
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $utilisateur = User::where('etablissement_id', $etablissementId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'telephone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que le rôle est autorisé
        $role = Role::find($request->role_id);
        $rolesAutorises = ['admin_etablissement', 'directeur_etudes', 'cpe', 'comptable', 'enseignant'];
        
        if (!in_array($role->name, $rolesAutorises)) {
            return redirect()->back()
                ->with('error', 'Rôle non autorisé pour un utilisateur d\'établissement.')
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'telephone' => $request->telephone,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $ancienRole = $utilisateur->role->name;
        $utilisateur->update($data);

        // Si le rôle change et devient enseignant, créer le profil enseignant
        if ($role->name === 'enseignant' && $ancienRole !== 'enseignant') {
            \App\Models\Enseignant::firstOrCreate(
                ['user_id' => $utilisateur->id],
                [
                    'matricule' => 'ENS' . str_pad($utilisateur->id, 5, '0', STR_PAD_LEFT),
                    'specialite' => 'Non définie',
                    'date_embauche' => now(),
                ]
            );
        }

        return redirect()->route('etablissement.utilisateurs.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur (désactive)
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $utilisateur = User::where('etablissement_id', $etablissementId)
            ->where('id', '!=', $user->id) // Ne pas pouvoir se supprimer soi-même
            ->findOrFail($id);
        
        // Désactiver plutôt que supprimer
        $utilisateur->update(['is_active' => false]);

        return redirect()->route('etablissement.utilisateurs.index')
            ->with('success', 'Utilisateur désactivé avec succès.');
    }

    /**
     * Active un utilisateur
     */
    public function activate($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $utilisateur = User::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        $utilisateur->update(['is_active' => true]);

        return redirect()->route('etablissement.utilisateurs.index')
            ->with('success', 'Utilisateur activé avec succès.');
    }

    /**
     * Réinitialise le mot de passe
     */
    public function resetPassword($id)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $utilisateur = User::where('etablissement_id', $etablissementId)
            ->findOrFail($id);
        
        // Générer un mot de passe aléatoire
        $nouveauMotDePasse = substr(md5(uniqid()), 0, 8);
        
        $utilisateur->update([
            'password' => Hash::make($nouveauMotDePasse)
        ]);

        return redirect()->route('etablissement.utilisateurs.index')
            ->with('success', "Mot de passe réinitialisé. Nouveau mot de passe : {$nouveauMotDePasse}");
    }

    /**
     * Exporte la liste des utilisateurs
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $utilisateurs = User::where('etablissement_id', $etablissementId)
            ->with('role')
            ->orderBy('name')
            ->get();
        
        $filename = 'utilisateurs-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['Nom', 'Email', 'Téléphone', 'Rôle', 'Statut']);
        
        foreach ($utilisateurs as $utilisateur) {
            fputcsv($handle, [
                $utilisateur->name,
                $utilisateur->email,
                $utilisateur->telephone ?? 'N/A',
                $utilisateur->role->display_name ?? $utilisateur->role->name,
                $utilisateur->is_active ? 'Actif' : 'Inactif',
            ]);
        }
        
        fclose($handle);
        exit;
    }
}