<?php
// app/Http/Middleware/ManagerMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerMiddleware
{
    /**
     * Rôles autorisés avec leurs niveaux hiérarchiques
     */
    protected $roleHierarchy = [
        'super_admin' => 100,
        'admin_etablissement' => 80,
        'directeur_etudes' => 70,
        'cpe' => 60,
        'comptable' => 50,
        'enseignant' => 40,
        'parent' => 20,
        'eleve' => 10,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|null  $minLevel
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $minLevel = 50)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur a un rôle
        if (!$user->role) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Aucun rôle assigné.'], 403);
            }
            abort(403, 'Aucun rôle assigné à cet utilisateur.');
        }

        // Récupérer le niveau du rôle de l'utilisateur
        $userLevel = $this->roleHierarchy[$user->role->name] ?? 0;

        // Vérifier si l'utilisateur a un niveau suffisant
        if ($userLevel >= (int) $minLevel) {
            return $next($request);
        }

        // Message d'erreur adapté
        $message = "Accès non autorisé. Niveau hiérarchique insuffisant.";
        
        if ($request->expectsJson()) {
            return response()->json(['error' => $message], 403);
        }
        
        abort(403, $message);
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     */
    public function hasRole($user, array $roles)
    {
        return in_array($user->role->name, $roles);
    }

    /**
     * Vérifier si l'utilisateur a un niveau supérieur à un autre utilisateur
     */
    public function hasHigherLevelThan($user, $otherUser)
    {
        $userLevel = $this->roleHierarchy[$user->role->name] ?? 0;
        $otherLevel = $this->roleHierarchy[$otherUser->role->name] ?? 0;
        
        return $userLevel > $otherLevel;
    }
}