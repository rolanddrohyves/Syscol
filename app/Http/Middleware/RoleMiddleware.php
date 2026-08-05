<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Vérifier si l'utilisateur a un rôle
        if (!$user->role) {
            abort(403, 'Aucun rôle assigné à cet utilisateur.');
        }

        // Vérifier si le rôle de l'utilisateur est dans la liste des rôles autorisés
        foreach ($roles as $role) {
            if ($user->role->name === $role) {
                return $next($request);
            }
        }

        // Si aucun rôle ne correspond
        abort(403, 'Accès non autorisé. Rôle requis : ' . implode(', ', $roles));
    }
}