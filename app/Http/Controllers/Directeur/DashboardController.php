<?php
// app/Http/Controllers/Directeur/DashboardController.php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Eleve;
use App\Models\EmploiTemps;
use App\Models\Note;
use App\Models\Absence;
use App\Models\AnneeScolaire;
use App\Models\Trimestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du directeur des études
     */
    public function index()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        // Récupérer l'année scolaire en cours
        $anneeEnCours = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->where('is_current', true)
            ->first();
        
        // Récupérer le trimestre en cours
        $trimestreEnCours = Trimestre::whereHas('anneeScolaire', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId)
                  ->where('is_current', true);
            })
            ->where('is_current', true)
            ->first();
        
        // ✅ CORRECTION : Compter les enseignants via la relation avec l'utilisateur
        $totalEnseignants = Enseignant::whereHas('user', function($q) use ($etablissementId) {
            $q->where('etablissement_id', $etablissementId);
        })->count();
        
        // Statistiques générales
        $stats = [
            'total_classes' => Classe::where('etablissement_id', $etablissementId)->count(),
            'total_enseignants' => $totalEnseignants,
            'total_eleves' => Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->count(),
            'total_notes' => Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->count(),
        ];
        
        // Statistiques des notes
        $statsNotes = [
            'moyenne_generale' => Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->avg('note') ?? 0,
            'meilleure_note' => Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->max('note') ?? 0,
            'moins_bonne_note' => Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->min('note') ?? 0,
        ];
        
        // Répartition des notes
        $totalNotes = $stats['total_notes'] > 0 ? $stats['total_notes'] : 1;
        $notesParTranche = [
            '0-5' => Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->whereBetween('note', [0, 5])->count(),
            '5-10' => Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->whereBetween('note', [5, 10])->count(),
            '10-15' => Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->whereBetween('note', [10, 15])->count(),
            '15-20' => Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->whereBetween('note', [15, 20])->count(),
        ];
        
        // Dernières notes saisies
        $dernieresNotes = Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'matiere', 'enseignant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Statistiques des absences
        $absencesMois = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereMonth('date', now()->month)
            ->count();
        
        $retardsMois = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('type', 'retard')
            ->whereMonth('date', now()->month)
            ->count();
        
        // Classes avec leurs effectifs
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->withCount('eleves')
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        // Emploi du temps du jour
        $emploiDuTemps = EmploiTemps::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['classe', 'matiere', 'enseignant'])
            ->where('jour', now()->locale('fr')->dayName)
            ->orderBy('heure_debut')
            ->limit(10)
            ->get();
        
        return view('directeur.dashboard', compact(
            'stats',
            'statsNotes',
            'notesParTranche',
            'dernieresNotes',
            'absencesMois',
            'retardsMois',
            'classes',
            'emploiDuTemps',
            'anneeEnCours',
            'trimestreEnCours'
        ));
    }

    /**
     * Affiche les statistiques détaillées
     */
    public function statistiques()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        // Performances par classe
        $performancesClasses = Classe::where('etablissement_id', $etablissementId)
            ->with(['eleves.notes'])
            ->get()
            ->map(function($classe) {
                $moyenne = $classe->eleves->flatMap->notes->avg('note') ?? 0;
                return [
                    'nom' => $classe->nom,
                    'moyenne' => round($moyenne, 2),
                    'effectif' => $classe->eleves->count(),
                ];
            })
            ->sortByDesc('moyenne')
            ->values();
        
        // Performances par matière
        $performancesMatieres = DB::table('notes')
            ->join('matieres', 'notes.matiere_id', '=', 'matieres.id')
            ->join('eleves', 'notes.eleve_id', '=', 'eleves.id')
            ->join('classes', 'eleves.classe_id', '=', 'classes.id')
            ->where('classes.etablissement_id', $etablissementId)
            ->select('matieres.nom', DB::raw('AVG(notes.note) as moyenne'), DB::raw('COUNT(notes.id) as nombre_notes'))
            ->groupBy('matieres.id', 'matieres.nom')
            ->orderBy('moyenne', 'desc')
            ->get();
        
        return response()->json([
            'performances_classes' => $performancesClasses,
            'performances_matieres' => $performancesMatieres,
        ]);
    }

    /**
     * Affiche l'emploi du temps de la semaine
     */
    public function emploiDuTemps()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        
        $emploiDuTemps = [];
        foreach ($jours as $jour) {
            $emploiDuTemps[$jour] = EmploiTemps::whereHas('classe', function($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId);
                })
                ->with(['classe', 'matiere', 'enseignant'])
                ->where('jour', $jour)
                ->orderBy('heure_debut')
                ->get();
        }
        
        return view('directeur.emploi-du-temps', compact('emploiDuTemps', 'jours'));
    }

    /**
     * Affiche le calendrier des examens
     */
    public function calendrierExamens()
    {
        // À implémenter selon votre modèle d'examen
        return view('directeur.calendrier-examens');
    }

    /**
     * Génère un rapport de performance
     */
    public function rapportPerformance()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        // Données pour le rapport
        $data = [
            'classes' => Classe::where('etablissement_id', $etablissementId)
                ->withCount('eleves')
                ->get(),
            'moyennes_classes' => $this->getMoyennesClasses(),
            'evolution_notes' => $this->getEvolutionNotes(),
        ];
        
        return view('directeur.rapport-performance', compact('data'));
    }

    /**
     * Récupère les moyennes par classe
     */
    private function getMoyennesClasses()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        return Classe::where('etablissement_id', $etablissementId)
            ->with(['eleves.notes'])
            ->get()
            ->mapWithKeys(function($classe) {
                $moyenne = $classe->eleves->flatMap->notes->avg('note') ?? 0;
                return [$classe->nom => round($moyenne, 2)];
            });
    }

    /**
     * Récupère l'évolution des notes dans le temps
     */
    private function getEvolutionNotes()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        return Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('AVG(note) as moyenne'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
    }
}