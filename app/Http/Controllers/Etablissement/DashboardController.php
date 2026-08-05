<?php
// app/Http/Controllers/Etablissement/DashboardController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\User;
use App\Models\Matiere;
use App\Models\EmploiTemps;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'établissement
     */
    public function index($etablissementId)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a accès à cet établissement
        if ($user->etablissement_id != $etablissementId && !$user->isSuperAdmin()) {
            abort(403, 'Accès non autorisé à cet établissement.');
        }

        // Statistiques générales
        $stats = [
            'classes' => $this->getClassesStats($etablissementId),
            'eleves' => $this->getElevesStats($etablissementId),
            'enseignants' => $this->getEnseignantsStats($etablissementId),
            'presences' => $this->getPresencesStats($etablissementId),
        ];

        // Données pour les graphiques
        $charts = [
            'repartition_classes' => $this->getRepartitionClasses($etablissementId),
            'evolution_eleves' => $this->getEvolutionEleves($etablissementId),
            'absences_hebdo' => $this->getAbsencesHebdomadaires($etablissementId),
        ];

        // Activités récentes
        $activitesRecentes = $this->getActivitesRecentes($etablissementId);

        // Alertes
        $alertes = $this->getAlertes($etablissementId);

        return view('etablissement.dashboard', compact('stats', 'charts', 'activitesRecentes', 'alertes'));
    }

    /**
     * Statistiques des classes
     */
    private function getClassesStats($etablissementId)
    {
        $classes = Classe::where('etablissement_id', $etablissementId);
        
        return [
            'total' => $classes->count(),
            'avec_pp' => $classes->whereNotNull('professeur_principal_id')->count(),
            'capacite_totale' => $classes->sum('capacite'),
            'par_niveau' => $classes->selectRaw('niveau, count(*) as total')
                ->groupBy('niveau')
                ->get()
                ->pluck('total', 'niveau')
                ->toArray(),
        ];
    }

    /**
     * Statistiques des élèves
     */
    private function getElevesStats($etablissementId)
    {
        $eleves = Eleve::whereHas('classe', function($q) use ($etablissementId) {
            $q->where('etablissement_id', $etablissementId);
        });

        return [
            'total' => $eleves->count(),
            'actifs' => $eleves->where('status', 'actif')->count(),
            'nouveaux_mois' => $eleves->whereMonth('created_at', now()->month)->count(),
            'filles' => $eleves->where('sexe', 'F')->count(),
            'garcons' => $eleves->where('sexe', 'M')->count(),
        ];
    }

    /**
     * Statistiques des enseignants
     */
    private function getEnseignantsStats($etablissementId)
    {
        $enseignants = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'));

        return [
            'total' => $enseignants->count(),
            'actifs' => $enseignants->where('is_active', true)->count(),
            'avec_classes' => $enseignants->whereHas('classes')->count(),
        ];
    }

    /**
     * Statistiques des présences
     */
    private function getPresencesStats($etablissementId)
    {
        $aujourdhui = Carbon::today();
        $classeIds = Classe::where('etablissement_id', $etablissementId)->pluck('id');
        
        $absencesAujourdhui = Absence::whereIn('classe_id', $classeIds)
            ->whereDate('date', $aujourdhui)
            ->count();

        $totalEleves = Eleve::whereIn('classe_id', $classeIds)->count();

        return [
            'absences_ajd' => $absencesAujourdhui,
            'presences_ajd' => $totalEleves - $absencesAujourdhui,
            'taux_presence' => $totalEleves > 0 
                ? round((($totalEleves - $absencesAujourdhui) / $totalEleves) * 100, 1)
                : 0,
        ];
    }

    /**
     * Répartition des classes par niveau (pour graphique)
     */
    private function getRepartitionClasses($etablissementId)
    {
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->withCount('eleves')
            ->get();

        $labels = [];
        $data = [];

        foreach ($classes as $classe) {
            $labels[] = $classe->nom;
            $data[] = $classe->eleves_count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Évolution des inscriptions (12 derniers mois)
     */
    private function getEvolutionEleves($etablissementId)
    {
        $evolution = [];
        $mois = [];
        $classeIds = Classe::where('etablissement_id', $etablissementId)->pluck('id');

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois[] = $date->format('M Y');
            
            $evolution[] = Eleve::whereIn('classe_id', $classeIds)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'mois' => $mois,
            'data' => $evolution,
        ];
    }

    /**
     * Absences hebdomadaires
     */
    private function getAbsencesHebdomadaires($etablissementId)
    {
        $jours = [];
        $absences = [];
        $classeIds = Classe::where('etablissement_id', $etablissementId)->pluck('id');

        $joursFrancais = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche',
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $jourAnglais = $date->format('l');
            $jours[] = $joursFrancais[$jourAnglais] ?? $jourAnglais;
            
            $absences[] = Absence::whereIn('classe_id', $classeIds)
                ->whereDate('date', $date)
                ->count();
        }

        return [
            'jours' => $jours,
            'data' => $absences,
        ];
    }

    /**
     * Activités récentes
     */
    private function getActivitesRecentes($etablissementId)
    {
        // À implémenter avec ActivityLog si disponible
        return [
            [
                'icon' => 'user-plus',
                'color' => 'green',
                'message' => 'Nouvel élève inscrit en 6ème A',
                'time' => 'Il y a 2 heures',
            ],
            [
                'icon' => 'chalkboard-teacher',
                'color' => 'blue',
                'message' => 'Nouvel enseignant affecté',
                'time' => 'Il y a 5 heures',
            ],
            [
                'icon' => 'calendar-check',
                'color' => 'purple',
                'message' => 'Emploi du temps mis à jour',
                'time' => 'Hier',
            ],
        ];
    }

    /**
     * Alertes
     */
    private function getAlertes($etablissementId)
    {
        $alertes = [];

        // Classes sans professeur principal
        $classesSansPP = Classe::where('etablissement_id', $etablissementId)
            ->whereNull('professeur_principal_id')
            ->count();

        if ($classesSansPP > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "{$classesSansPP} classe(s) sans professeur principal",
            ];
        }

        // Classes presque pleines (>90%)
        $classesPleines = Classe::where('etablissement_id', $etablissementId)
            ->withCount('eleves')
            ->get()
            ->filter(function($classe) {
                return $classe->capacite > 0 && 
                       ($classe->eleves_count / $classe->capacite) > 0.9;
            })
            ->count();

        if ($classesPleines > 0) {
            $alertes[] = [
                'type' => 'info',
                'message' => "{$classesPleines} classe(s) presque pleines",
            ];
        }

        return $alertes;
    }

    /**
     * Rafraîchit les données du dashboard (AJAX)
     */
    public function refresh($etablissementId)
    {
        $stats = [
            'classes' => $this->getClassesStats($etablissementId),
            'eleves' => $this->getElevesStats($etablissementId),
            'enseignants' => $this->getEnseignantsStats($etablissementId),
            'presences' => $this->getPresencesStats($etablissementId),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'alertes' => $this->getAlertes($etablissementId),
        ]);
    }

    /**
     * Export PDF du dashboard
     */
    public function exportPdf($etablissementId)
    {
        $stats = [
            'classes' => $this->getClassesStats($etablissementId),
            'eleves' => $this->getElevesStats($etablissementId),
            'enseignants' => $this->getEnseignantsStats($etablissementId),
            'presences' => $this->getPresencesStats($etablissementId),
        ];

        $pdf = Pdf::loadView('etablissement.dashboard-pdf', compact('stats'));
        
        return $pdf->download('dashboard-' . now()->format('Y-m-d') . '.pdf');
    }
}