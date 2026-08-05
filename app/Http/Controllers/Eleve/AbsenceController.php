<?php
// app/Http/Controllers/Eleve/AbsenceController.php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Absence;
use App\Models\Eleve;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Méthode 1: Chercher par user_id
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        // Méthode 2: Chercher par email
        if (!$eleve) {
            $eleve = Eleve::where('email', $user->email)->first();
        }
        
        // Méthode 3: Chercher par email_parent
        if (!$eleve) {
            $eleve = Eleve::where('email_parent', $user->email)->first();
        }
        
        // Méthode 4: Si l'utilisateur a un rôle eleve, créer le profil automatiquement
        if (!$eleve && $user->role && $user->role->name == 'eleve') {
            $eleve = $this->createEleveFromUser($user);
        }
        
        if (!$eleve) {
            return view('eleve.absences')->with('error', 'Profil élève non trouvé. Veuillez contacter l\'administrateur.');
        }
        
        // Récupérer les absences
        $absences = Absence::where('eleve_id', $eleve->id)
            ->with(['enseignant'])
            ->orderBy('date', 'desc')
            ->paginate(15);
        
        // Statistiques
        $stats = [
            'total_absences' => Absence::where('eleve_id', $eleve->id)->where('type', 'absence')->count(),
            'total_retards' => Absence::where('eleve_id', $eleve->id)->where('type', 'retard')->count(),
            'absences_justifiees' => Absence::where('eleve_id', $eleve->id)->where('type', 'absence')->where('justifiee', true)->count(),
            'absences_non_justifiees' => Absence::where('eleve_id', $eleve->id)->where('type', 'absence')->where('justifiee', false)->count(),
        ];
        
        return view('eleve.absences', compact('eleve', 'absences', 'stats'));
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
            \Log::error('Erreur création profil élève: ' . $e->getMessage());
            return null;
        }
    }
}