<?php
// app/Http/Controllers/Eleve/EmploiTempsController.php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmploiTemps;
use App\Models\Eleve;

class EmploiTempsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Méthodes multiples pour trouver l'élève
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        if (!$eleve) {
            $eleve = Eleve::where('email', $user->email)->first();
        }
        
        if (!$eleve) {
            $eleve = Eleve::where('email_parent', $user->email)->first();
        }
        
        if (!$eleve && $user->role && $user->role->name == 'eleve') {
            $eleve = $this->createEleveFromUser($user);
        }
        
        if (!$eleve) {
            return view('eleve.emploi-temps')->with('error', 'Profil élève non trouvé.');
        }
        
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        
        $tousLesCours = EmploiTemps::where('classe_id', $eleve->classe_id)
            ->with(['matiere', 'enseignant'])
            ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
            ->orderBy('heure_debut')
            ->get();
        
        $emploiDuTemps = [];
        foreach ($jours as $jour) {
            $emploiDuTemps[$jour] = $tousLesCours->where('jour', $jour)->values();
        }
        
        return view('eleve.emploi-temps', compact('emploiDuTemps', 'jours', 'eleve'));
    }
    
    private function createEleveFromUser($user)
    {
        $classe = \App\Models\Classe::first();
        
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
            return null;
        }
    }
}