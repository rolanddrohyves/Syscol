<?php
// app/Http/Controllers/Eleve/DashboardController.php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Note;
use App\Models\EmploiTemps;
use App\Models\Absence;
use App\Models\Bulletin;
use App\Models\Devoir;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'élève
     */
    public function index()
    {
        $user = Auth::user();
        
        // Chercher l'élève par user_id
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        // Si l'élève n'existe pas, essayer de le créer automatiquement
        if (!$eleve) {
            $eleve = $this->createEleveFromUser($user);
        }
        
        // Si toujours pas d'élève, afficher un message d'erreur
        if (!$eleve) {
            return view('eleve.dashboard')->withErrors([
                'error' => 'Profil élève non trouvé. Veuillez contacter l\'administrateur.'
            ]);
        }
        
        // Récupérer les notes récentes
        $notesRecentes = Note::where('eleve_id', $eleve->id)
            ->with(['matiere'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Calculer la moyenne générale
        $moyenneGenerale = Note::where('eleve_id', $eleve->id)
            ->avg('note');
        
        // Récupérer l'emploi du temps du jour
        $joursSemaine = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche'
        ];
        $aujourdhui = $joursSemaine[now()->format('l')] ?? now()->format('l');
        
        $emploiDuTemps = EmploiTemps::where('classe_id', $eleve->classe_id)
            ->where('jour', $aujourdhui)
            ->with(['matiere', 'enseignant'])
            ->orderBy('heure_debut')
            ->get();
        
        // Récupérer les absences non justifiées
        $absencesNonJustifiees = Absence::where('eleve_id', $eleve->id)
            ->where('justifiee', false)
            ->count();
        
        // Récupérer les devoirs à rendre
        $devoirs = Devoir::where('classe_id', $eleve->classe_id)
            ->whereDate('date_limite', '>=', now())
            ->with(['matiere'])
            ->orderBy('date_limite', 'asc')
            ->take(5)
            ->get();
        
        // Récupérer les bulletins récents
        $bulletins = Bulletin::where('eleve_id', $eleve->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Statistiques
        $stats = [
            'total_notes' => Note::where('eleve_id', $eleve->id)->count(),
            'moyenne_generale' => round($moyenneGenerale ?? 0, 2),
            'absences_non_justifiees' => $absencesNonJustifiees,
            'cours_aujourdhui' => $emploiDuTemps->count(),
            'devoirs_a_rendre' => $devoirs->count(),
        ];
        
        return view('eleve.dashboard', compact(
            'eleve', 
            'notesRecentes', 
            'emploiDuTemps', 
            'stats', 
            'bulletins', 
            'devoirs'
        ));
    }
    
    /**
     * Crée automatiquement un profil élève à partir d'un utilisateur
     */
    private function createEleveFromUser($user)
    {
        // Récupérer une classe existante
        $classe = Classe::first();
        
        if (!$classe) {
            return null;
        }
        
        // Créer le profil élève
        try {
            $eleve = Eleve::create([
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
            
            return $eleve;
            
        } catch (\Exception $e) {
            \Log::error('Erreur création profil élève: ' . $e->getMessage());
            return null;
        }
    }
}