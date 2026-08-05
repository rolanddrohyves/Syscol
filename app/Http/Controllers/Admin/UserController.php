<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'etablissement']);
        
        // Filtres
        if ($request->has('role') && $request->role != '') {
            $query->where('role_id', $request->role);
        }
        
        if ($request->has('etablissement') && $request->etablissement != '') {
            $query->where('etablissement_id', $request->etablissement);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Pour les filtres
        $roles = Role::all();
        $etablissements = Etablissement::all();
        
        $stats = [
            'total' => User::count(),
            'actifs' => User::where('is_active', true)->count(),
            'inactifs' => User::where('is_active', false)->count(),
            'nouveaux' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];
        
        return view('admin.users.index', compact('users', 'roles', 'etablissements', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        $roles = Role::all();
        $etablissements = Etablissement::all();
        
        return view('admin.users.create', compact('roles', 'etablissements'));
    }

    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'etablissement_id' => 'nullable|exists:etablissements,id',
            'telephone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'etablissement_id' => $request->etablissement_id,
            'telephone' => $request->telephone,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur
     */
    public function show($id)
    {
        $user = User::with(['role', 'etablissement'])->findOrFail($id);
        
        // Statistiques de l'utilisateur
        $stats = [
            'date_inscription' => $user->created_at->format('d/m/Y'),
            'derniere_connexion' => $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais',
            'actions' => 0, // À implémenter selon votre logique
        ];
        
        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $etablissements = Etablissement::all();
        
        return view('admin.users.edit', compact('user', 'roles', 'etablissements'));
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'etablissement_id' => 'nullable|exists:etablissements,id',
            'telephone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'etablissement_id' => $request->etablissement_id,
            'telephone' => $request->telephone,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher la suppression de son propre compte
        if ($user->id == auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Empêcher la suppression des super admins
        if ($user->role->name === 'super_admin' && User::whereHas('role', fn($q) => $q->where('name', 'super_admin'))->count() <= 1) {
            return redirect()->route('admin.users')
                ->with('error', 'Impossible de supprimer le dernier super administrateur.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Active ou désactive un utilisateur
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Empêcher la désactivation de son propre compte
        if ($user->id == auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Vous ne pouvez pas modifier le statut de votre propre compte.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activé' : 'désactivé';
        
        return redirect()->route('admin.users')
            ->with('success', "Utilisateur {$status} avec succès.");
    }

    /**
     * Réinitialise le mot de passe d'un utilisateur
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        $newPassword = 'password123'; // Générer un mot de passe aléatoire
        $user->password = Hash::make($newPassword);
        $user->save();

        return redirect()->route('admin.users')
            ->with('success', "Mot de passe réinitialisé. Nouveau mot de passe : {$newPassword}");
    }

    /**
     * Exporte la liste des utilisateurs
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $users = User::with(['role', 'etablissement'])->get();
        
        switch($format) {
            case 'csv':
                return $this->exportCsv($users);
            case 'pdf':
                return redirect()->back()->with('info', 'Export PDF en cours de développement');
            default:
                return redirect()->back()->with('error', 'Format non supporté');
        }
    }

    /**
     * Export CSV
     */
    private function exportCsv($users)
    {
        $filename = "utilisateurs_" . date('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // En-têtes
        fputcsv($handle, ['ID', 'Nom', 'Email', 'Rôle', 'Établissement', 'Téléphone', 'Statut', 'Inscription']);
        
        // Données
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->role->display_name ?? $user->role->name ?? 'N/A',
                $user->etablissement->nom ?? 'N/A',
                $user->telephone ?? 'N/A',
                $user->is_active ? 'Actif' : 'Inactif',
                $user->created_at->format('d/m/Y'),
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Recherche d'utilisateurs (pour AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->with('role')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name . ' (' . $user->email . ')',
                ];
            });
        
        return response()->json($users);
    }

    /**
     * Actions en masse
     */
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Aucun utilisateur sélectionné.');
        }

        switch ($action) {
            case 'activate':
                User::whereIn('id', $ids)->update(['is_active' => true]);
                $message = 'Utilisateurs activés avec succès.';
                break;
            case 'deactivate':
                // Empêcher la désactivation de son propre compte
                if (in_array(auth()->id(), $ids)) {
                    return redirect()->back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
                }
                User::whereIn('id', $ids)->update(['is_active' => false]);
                $message = 'Utilisateurs désactivés avec succès.';
                break;
            case 'delete':
                // Empêcher la suppression de son propre compte
                if (in_array(auth()->id(), $ids)) {
                    return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
                }
                User::whereIn('id', $ids)->delete();
                $message = 'Utilisateurs supprimés avec succès.';
                break;
            default:
                return redirect()->back()->with('error', 'Action non reconnue.');
        }

        return redirect()->route('admin.users')->with('success', $message);
    }
}