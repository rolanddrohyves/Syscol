<?php
// app/Http/Controllers/Eleve/BulletinController.php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Eleve;
use App\Models\Bulletin;
use App\Models\Classe;

class BulletinController extends Controller
{
    /**
     * Affiche la liste des bulletins
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer l'élève connecté
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        // Si l'élève n'existe pas, essayer de le créer
        if (!$eleve) {
            $eleve = $this->createEleveFromUser($user);
        }
        
        // Si toujours pas d'élève, afficher un message
        if (!$eleve) {
            return view('eleve.bulletins')->withErrors([
                'error' => 'Profil élève non trouvé. Veuillez contacter l\'administrateur.'
            ]);
        }
        
        // Récupérer les bulletins de l'élève
        $bulletins = Bulletin::where('eleve_id', $eleve->id)
            ->with(['trimestre', 'anneeScolaire'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques
        $stats = [
            'total_bulletins' => $bulletins->count(),
            'moyenne_generale' => $bulletins->avg('moyenne_generale') ?? 0,
        ];
        
        return view('eleve.bulletins', compact('eleve', 'bulletins', 'stats'));
    }
    
    /**
     * Affiche un bulletin spécifique
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Récupérer l'élève
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        if (!$eleve) {
            return redirect()->route('eleve.bulletins')->withErrors([
                'error' => 'Profil élève non trouvé.'
            ]);
        }
        
        // Récupérer le bulletin
        $bulletin = Bulletin::where('id', $id)
            ->where('eleve_id', $eleve->id)
            ->with(['trimestre', 'anneeScolaire', 'notes.matiere'])
            ->first();
        
        if (!$bulletin) {
            return redirect()->route('eleve.bulletins')->withErrors([
                'error' => 'Bulletin non trouvé.'
            ]);
        }
        
        return view('eleve.bulletin-show', compact('bulletin', 'eleve'));
    }
    
    /**
     * Crée automatiquement un profil élève
     */
    private function createEleveFromUser($user)
    {
        $classe = Classe::first();
        
        if (!$classe) {
            return null;
        }
        
        try {
            return Eleve::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'email_parent' => $user->email,
                'nom' => explode(' ', $user->name)[1] ?? $user->name,
                'prenom' => explode(' ', $user->name)[0] ?? 'Élève',
                'matricule' => 'MAT_' . $user->id . '_' . time(),
                'classe_id' => $classe->id,
                'date_naissance' => '2000-01-01',
                'lieu_naissance' => 'Inconnu',
                'sexe' => 'M',
                'adresse' => 'Non renseignée',
                'telephone_parent' => $user->telephone ?? 'Non renseigné',
                'nom_parent' => $user->name,
                'status' => 'actif'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur création profil élève: ' . $e->getMessage());
            return null;
        }
    }
}