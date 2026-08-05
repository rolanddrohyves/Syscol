<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $level
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $level = null)
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

        // Vérifier le niveau d'admin si spécifié
        if ($level) {
            $adminLevels = [
                'super' => 'super_admin',
                'etablissement' => 'admin_etablissement',
                'all' => ['super_admin', 'admin_etablissement']
            ];

            $requiredRoles = $adminLevels[$level] ?? $level;
            $requiredRoles = (array) $requiredRoles;

            if (!in_array($user->role->name, $requiredRoles)) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Niveau d\'administration insuffisant.'], 403);
                }
                abort(403, 'Niveau d\'administration insuffisant.');
            }
        } else {
            // Vérification admin simple
            if (!in_array($user->role->name, ['super_admin', 'admin_etablissement'])) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Accès administrateur requis.'], 403);
                }
                abort(403, 'Accès non autorisé. Vous devez être administrateur.');
            }
        }

        return $next($request);
    }
}