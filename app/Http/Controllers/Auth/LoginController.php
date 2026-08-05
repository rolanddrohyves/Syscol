<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validation
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tentative de connexion
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Récupérer l'utilisateur
            $user = Auth::user();

            // ✅ SOLUTION: Mettre à jour sans déclencher les logs
            $this->updateLastLoginWithoutLogging($user, $request);

            // Journaliser la connexion réussie
            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'role' => $user->role->name ?? 'inconnu'
                ])
                ->event('login')
                ->log('Connexion réussie');

            return $this->redirectBasedOnRole();
        }

        // Journaliser l'échec de connexion
        activity()
            ->withProperties([
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->event('login_failed')
            ->log('Tentative de connexion échouée');

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Met à jour last_login_at sans déclencher les logs
     */
    private function updateLastLoginWithoutLogging($user, $request)
    {
        // Désactiver temporairement les logs
        $enabled = config('activitylog.enabled');
        config(['activitylog.enabled' => false]);
        
        // Mettre à jour sans logs
        $user->last_login_at = now();
        $user->save();
        
        // Réactiver les logs
        config(['activitylog.enabled' => $enabled]);
    }

    protected function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        if (!$user->role) {
            Auth::logout();
            
            activity()
                ->causedBy($user)
                ->withProperties([
                    'email' => $user->email,
                    'user_id' => $user->id
                ])
                ->event('error')
                ->log('Utilisateur sans rôle');
                
            return redirect()->route('login')
                ->withErrors(['email' => 'Aucun rôle assigné à cet utilisateur.']);
        }

        if (!$user->is_active) {
            Auth::logout();
            
            activity()
                ->causedBy($user)
                ->withProperties([
                    'email' => $user->email,
                    'user_id' => $user->id
                ])
                ->event('error')
                ->log('Tentative de connexion sur compte inactif');
                
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été désactivé. Contactez l\'administrateur.']);
        }

        $roleName = $user->role->name;
        
        if ($user->isSuperAdmin() || in_array($roleName, ['inspecteur_general', 'directeur_regional'])) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Bienvenue Super Admin');
        }
        
        if ($user->isAdminEtablissement()) {
            if ($user->etablissement_id) {
                return redirect()->route('etablissement.dashboard', ['etablissement' => $user->etablissement_id])
                    ->with('success', 'Bienvenue sur votre établissement');
            }
            return redirect()->route('dashboard')->with('success', 'Bienvenue');
        }
        
        if ($user->isDirecteurEtudes()) {
            return redirect()->route('directeur.dashboard')
                ->with('success', 'Bienvenue Directeur des Études');
        }
        
        if ($user->isCpe()) {
            return redirect()->route('cpe.dashboard')
                ->with('success', 'Bienvenue CPE');
        }
        
        if ($user->isEnseignant()) {
            return redirect()->route('enseignant.dashboard')
                ->with('success', 'Bienvenue Enseignant');
        }
        
        if ($user->isParent()) {
            return redirect()->route('parent.dashboard')
                ->with('success', 'Bienvenue sur le suivi de votre enfant');
        }
        
        if ($user->isEleve()) {
            return redirect()->route('eleve.dashboard')
                ->with('success', 'Bienvenue sur votre espace élève');
        }
        
        return redirect()->intended('dashboard')->with('success', 'Bienvenue');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->event('logout')
                ->log('Déconnexion');
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}