<?php
// app/Http/Controllers/Admin/RoleController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Affiche la liste des rôles
     */
    public function index(Request $request)
    {
        $query = Role::withCount('users');
        
        // Recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $roles = $query->orderBy('level', 'desc')->paginate(15);
        
        $stats = [
            'total' => Role::count(),
            'utilisateurs' => User::count(),
            'moyenne_utilisateurs' => round(User::count() / max(Role::count(), 1), 1),
            'niveau_max' => Role::max('level'),
        ];
        
        return view('admin.roles.index', compact('roles', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Enregistre un nouveau rôle
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1|max:100',
        ], [
            'name.regex' => 'Le nom du rôle ne doit contenir que des lettres minuscules et des underscores',
            'name.unique' => 'Ce nom de rôle existe déjà',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'level' => $request->level,
        ]);

        return redirect()->route('admin.roles')
            ->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Affiche les détails d'un rôle
     */
    public function show($id)
    {
        $role = Role::with(['users' => function($q) {
                $q->orderBy('name')->limit(10);
            }])->findOrFail($id);
        
        $stats = [
            'total_utilisateurs' => $role->users()->count(),
            'utilisateurs_actifs' => $role->users()->where('is_active', true)->count(),
            'dernier_utilisateur' => $role->users()->latest()->first(),
            'niveau' => $role->level,
        ];
        
        return view('admin.roles.show', compact('role', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        
        // Empêcher la modification des rôles système
        if (in_array($role->name, ['super_admin', 'admin_etablissement', 'enseignant', 'eleve', 'parent', 'comptable', 'cpe', 'directeur_etudes'])) {
            return redirect()->route('admin.roles')
                ->with('error', 'Ce rôle système ne peut pas être modifié.');
        }
        
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Met à jour un rôle
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        // Empêcher la modification des rôles système
        if (in_array($role->name, ['super_admin', 'admin_etablissement', 'enseignant', 'eleve', 'parent', 'comptable', 'cpe', 'directeur_etudes'])) {
            return redirect()->route('admin.roles')
                ->with('error', 'Ce rôle système ne peut pas être modifié.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name,' . $id . '|regex:/^[a-z_]+$/',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1|max:100',
        ], [
            'name.regex' => 'Le nom du rôle ne doit contenir que des lettres minuscules et des underscores',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'level' => $request->level,
        ]);

        return redirect()->route('admin.roles')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Supprime un rôle
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Empêcher la suppression des rôles système
        if (in_array($role->name, ['super_admin', 'admin_etablissement', 'enseignant', 'eleve', 'parent', 'comptable', 'cpe', 'directeur_etudes'])) {
            return redirect()->route('admin.roles')
                ->with('error', 'Impossible de supprimer un rôle système.');
        }

        // Vérifier si des utilisateurs sont assignés à ce rôle
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles')
                ->with('error', 'Impossible de supprimer ce rôle car des utilisateurs y sont assignés.');
        }

        $role->delete();

        return redirect()->route('admin.roles')
            ->with('success', 'Rôle supprimé avec succès.');
    }

    /**
     * Affiche les utilisateurs d'un rôle spécifique
     */
    public function users($id)
    {
        $role = Role::findOrFail($id);
        $users = $role->users()->paginate(15);
        
        return view('admin.roles.users', compact('role', 'users'));
    }

    /**
     * Duplique un rôle
     */
    public function duplicate($id)
    {
        $originalRole = Role::findOrFail($id);
        
        $newRole = Role::create([
            'name' => $originalRole->name . '_copy',
            'display_name' => $originalRole->display_name . ' (Copie)',
            'description' => $originalRole->description,
            'level' => $originalRole->level,
        ]);

        return redirect()->route('admin.roles.edit', $newRole->id)
            ->with('success', 'Rôle dupliqué avec succès. Vous pouvez maintenant le modifier.');
    }

    /**
     * Exporte la liste des rôles
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $roles = Role::withCount('users')->get();
        
        switch($format) {
            case 'csv':
                return $this->exportCsv($roles);
            case 'pdf':
                return redirect()->back()->with('info', 'Export PDF en cours de développement');
            default:
                return redirect()->back()->with('error', 'Format non supporté');
        }
    }

    /**
     * Export CSV
     */
    private function exportCsv($roles)
    {
        $filename = "roles_" . date('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // En-têtes
        fputcsv($handle, ['ID', 'Nom technique', 'Nom affiché', 'Description', 'Niveau', 'Utilisateurs', 'Créé le']);
        
        // Données
        foreach ($roles as $role) {
            fputcsv($handle, [
                $role->id,
                $role->name,
                $role->display_name,
                $role->description,
                $role->level,
                $role->users_count,
                $role->created_at->format('d/m/Y'),
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Met à jour le niveau hiérarchique
     */
    public function updateLevel(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'level' => 'required|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->update(['level' => $request->level]);

        return response()->json([
            'success' => true,
            'message' => 'Niveau mis à jour avec succès.',
            'level' => $role->level
        ]);
    }

    /**
     * Recherche de rôles (pour AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $roles = Role::where('name', 'like', "%{$query}%")
            ->orWhere('display_name', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function($role) {
                return [
                    'id' => $role->id,
                    'text' => $role->display_name . ' (' . $role->name . ')',
                ];
            });
        
        return response()->json($roles);
    }
}