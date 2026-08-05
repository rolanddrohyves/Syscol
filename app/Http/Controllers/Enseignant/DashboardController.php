<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\EmploiTemps;
use App\Models\Note;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Trimestre;
use App\Models\AnneeScolaire;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $enseignant = Enseignant::with(['matieres', 'classes', 'emploisTemps'])
            ->where('user_id', $user->id)
            ->first();
        
        if (!$enseignant) {
            return redirect()->route('dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        $etablissementId = $user->etablissement_id;
        
        // Récupérer l'année scolaire en cours
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->orderBy('id', 'desc')
            ->first();
        
        // Récupérer le trimestre actuel
        $trimestreActuel = null;
        if ($anneeScolaire) {
            $trimestreActuel = Trimestre::where('annee_scolaire_id', $anneeScolaire->id)
                ->orderBy('id', 'asc')
                ->first();
        }
        
        // Récupérer les classes de l'enseignant
        $classesIds = EmploiTemps::where('enseignant_id', $enseignantId)
            ->distinct()
            ->pluck('classe_id');
        
        $classes = Classe::whereIn('id', $classesIds)
            ->with(['eleves', 'matieres'])
            ->get();
        
        $classesPrincipales = $enseignant->classes;
        $classes = $classes->merge($classesPrincipales)->unique('id');
        
        // Statistiques générales
        $stats = [
            'total_classes' => $classes->count(),
            'total_eleves' => $classes->sum(function($classe) {
                return $classe->eleves->count();
            }),
            'total_matieres' => $enseignant->matieres->count(),
            'total_notes' => Note::where('enseignant_id', $enseignantId)->count(),
        ];
        
        // Moyennes par matière
        $moyennesParMatiere = [];
        foreach ($enseignant->matieres as $matiere) {
            $moyenne = Note::where('enseignant_id', $enseignantId)
                ->where('matiere_id', $matiere->id)
                ->avg('note');
            
            if ($moyenne) {
                $moyennesParMatiere[] = [
                    'matiere' => $matiere->nom,
                    'moyenne' => round($moyenne, 2),
                ];
            }
        }
        
        // Emploi du temps du jour
        $aujourdhui = Carbon::now()->locale('fr')->dayName;
        
        $emploiDuJour = EmploiTemps::where('enseignant_id', $enseignantId)
            ->where('jour', $aujourdhui)
            ->with(['classe', 'matiere'])
            ->orderBy('heure_debut')
            ->get();
        
        // Cours à venir
        $ordreJours = [
            'Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3,
            'Jeudi' => 4, 'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 7,
        ];
        
        $jourActuel = $aujourdhui;
        $ordreActuel = $ordreJours[$jourActuel] ?? 0;
        $heureActuelle = Carbon::now()->format('H:i:s');
        
        $prochainsCours = EmploiTemps::where('enseignant_id', $enseignantId)
            ->where(function($query) use ($ordreJours, $ordreActuel, $jourActuel, $heureActuelle) {
                foreach ($ordreJours as $jour => $ordre) {
                    if ($ordre > $ordreActuel) {
                        $query->orWhere('jour', $jour);
                    }
                }
                $query->orWhere(function($sub) use ($jourActuel, $heureActuelle) {
                    $sub->where('jour', $jourActuel)
                        ->where('heure_debut', '>', $heureActuelle);
                });
            })
            ->with(['classe', 'matiere'])
            ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche')")
            ->orderBy('heure_debut')
            ->limit(5)
            ->get();
        
        // Dernières notes saisies
        $dernieresNotes = Note::where('enseignant_id', $enseignantId)
            ->with(['eleve', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Absences du jour par classe
        $absencesJour = [];
        foreach ($classes as $classe) {
            $totalAbsences = Absence::whereHas('eleve', function($q) use ($classe) {
                $q->where('classe_id', $classe->id);
            })->whereDate('date', Carbon::today())->count();
            
            $absencesJour[$classe->id] = [
                'classe' => $classe->nom,
                'total_absences' => $totalAbsences,
                'total_retards' => 0, // Colonne est_retard n'existe pas
            ];
        }
        
        // Taux de présence par classe - calcul basé sur les absences plutôt que les présences
        $tauxPresenceParClasse = [];
        foreach ($classes as $classe) {
            $totalEleves = $classe->eleves->count();
            
            // Compter le nombre d'absences totales
            $totalAbsences = Absence::whereHas('eleve', function($q) use ($classe) {
                $q->where('classe_id', $classe->id);
            })->count();
            
            // Nombre approximatif de jours de cours dans le trimestre
            $nbJoursCours = 60;
            $totalPresencesAttendues = $totalEleves * $nbJoursCours;
            
            // Calculer le taux de présence
            $taux = $totalPresencesAttendues > 0 
                ? round((($totalPresencesAttendues - $totalAbsences) / $totalPresencesAttendues) * 100, 2) 
                : 100;
            
            $tauxPresenceParClasse[] = [
                'classe' => $classe->nom,
                'taux' => $taux,
            ];
        }
        
        // Alertes
        $alertes = [];
        
        // Alertes pour les absences élevées aujourd'hui
        foreach ($absencesJour as $abs) {
            if ($abs['total_absences'] > 5) {
                $alertes[] = [
                    'type' => 'danger',
                    'message' => "Taux d'absence élevé dans la classe {$abs['classe']} aujourd'hui ({$abs['total_absences']} absent(s)).",
                    'lien' => '#'
                ];
                break;
            }
        }
        
        // Alerte pour les notes à saisir
        $notesASaisir = Note::where('enseignant_id', $enseignantId)
            ->whereNull('note')
            ->count();
        
        if ($notesASaisir > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "Vous avez {$notesASaisir} note(s) à saisir.",
                'lien' => route('enseignant.notes')
            ];
        }
        
        return view('enseignant.dashboard', compact(
            'stats',
            'moyennesParMatiere',
            'emploiDuJour',
            'prochainsCours',
            'dernieresNotes',
            'absencesJour',
            'tauxPresenceParClasse',
            'alertes',
            'anneeScolaire',
            'trimestreActuel',
            'classes',
            'enseignant'
        ));
    }
}