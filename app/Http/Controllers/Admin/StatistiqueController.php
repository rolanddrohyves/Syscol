<?php
// app/Http/Controllers/Admin/StatistiqueController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Role;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    /**
     * Affiche le tableau de bord des statistiques
     */
    public function index()
    {
        $stats = [
            'general' => $this->getGeneralStats(),
            'evolution' => $this->getEvolutionStats(),
            'repartition' => $this->getRepartitionStats(),
            'activite' => $this->getActiviteStats(),
            'top' => $this->getTopStats(),
            'performance' => $this->getPerformanceStats(),
        ];

        return view('admin.statistiques.index', compact('stats'));
    }

    /**
     * Statistiques générales
     */
    private function getGeneralStats()
    {
        return [
            'etablissements' => [
                'total' => Etablissement::count(),
                'actifs' => Etablissement::where('is_active', true)->count(),
                'nouveaux_mois' => Etablissement::whereMonth('created_at', now()->month)->count(),
            ],
            'utilisateurs' => [
                'total' => User::count(),
                'actifs' => User::where('is_active', true)->count(),
                'nouveaux_mois' => User::whereMonth('created_at', now()->month)->count(),
            ],
            'eleves' => [
                'total' => Eleve::count(),
                'actifs' => Eleve::where('status', 'actif')->count(),
                'nouveaux_mois' => Eleve::whereMonth('created_at', now()->month)->count(),
            ],
            'classes' => [
                'total' => Classe::count(),
                'avec_prof_principal' => Classe::whereNotNull('professeur_principal_id')->count(),
            ],
            'enseignants' => [
                'total' => User::whereHas('role', fn($q) => $q->where('name', 'enseignant'))->count(),
            ],
            'matieres' => [
                'total' => Matiere::count(),
            ],
        ];
    }

    /**
     * Statistiques d'évolution (graphiques)
     */
    private function getEvolutionStats()
    {
        // Évolution des inscriptions sur les 12 derniers mois
        $evolutionEleves = [];
        $evolutionUtilisateurs = [];
        $mois = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois[] = $date->format('M Y');
            
            $evolutionEleves[] = Eleve::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $evolutionUtilisateurs[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'mois' => $mois,
            'eleves' => $evolutionEleves,
            'utilisateurs' => $evolutionUtilisateurs,
        ];
    }

    /**
     * Statistiques de répartition - VERSION CORRIGÉE
     */
    private function getRepartitionStats()
    {
        // Répartition par type d'établissement
        $etablissementsParType = Etablissement::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        // Répartition par rôle
        $utilisateursParRole = Role::withCount('users')
            ->get()
            ->map(fn($role) => [
                'role' => $role->display_name,
                'total' => $role->users_count,
            ]);

        // Récupérer tous les niveaux distincts
        $niveaux = Classe::distinct()->pluck('niveau');
        $elevesParNiveau = [];

        foreach ($niveaux as $niveau) {
            // Récupérer les classes de ce niveau
            $classes = Classe::where('niveau', $niveau)->get();
            $classeIds = $classes->pluck('id');
            
            // Calculer les statistiques
            $totalClasses = $classes->count();
            $totalCapacite = $classes->sum('capacite');
            $totalEleves = Eleve::whereIn('classe_id', $classeIds)->count();
            
            $elevesParNiveau[] = [
                'niveau' => $niveau,
                'classes' => $totalClasses,
                'eleves' => $totalEleves,
                'capacite' => $totalCapacite,
                'taux_occupation' => $totalCapacite > 0 
                    ? round(($totalEleves / $totalCapacite) * 100, 1) 
                    : 0,
            ];
        }

        return [
            'etablissements' => $etablissementsParType,
            'utilisateurs' => $utilisateursParRole,
            'eleves' => $elevesParNiveau,
        ];
    }

    /**
     * Statistiques d'activité
     */
    private function getActiviteStats()
    {
        // Connexions récentes
        $connexionsRecentes = User::where('last_login_at', '>=', now()->subDays(7))
            ->count();

        // Utilisateurs actifs aujourd'hui
        $actifsAujourdhui = User::where('last_login_at', '>=', now()->startOfDay())
            ->count();

        return [
            'connexions_7j' => $connexionsRecentes,
            'actifs_aujourdhui' => $actifsAujourdhui,
            'taux_activite' => User::count() > 0 
                ? round(($actifsAujourdhui / User::count()) * 100, 1)
                : 0,
        ];
    }

    /**
     * Top statistiques
     */
    private function getTopStats()
    {
        // Établissements avec le plus d'élèves
        $topEtablissements = Etablissement::withCount('eleves')
            ->orderBy('eleves_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($etab) => [
                'nom' => $etab->nom,
                'total' => $etab->eleves_count,
            ]);

        // Classes avec le plus d'élèves
        $topClasses = Classe::withCount('eleves')
            ->with('etablissement')
            ->orderBy('eleves_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($classe) => [
                'nom' => $classe->nom . ' (' . $classe->etablissement->nom . ')',
                'total' => $classe->eleves_count,
                'capacite' => $classe->capacite,
            ]);

        return [
            'etablissements' => $topEtablissements,
            'classes' => $topClasses,
        ];
    }

    /**
     * Statistiques de performance
     */
    private function getPerformanceStats()
    {
        // Taux de remplissage global
        $totalCapacite = Classe::sum('capacite');
        $totalEleves = Eleve::count();
        $tauxRemplissage = $totalCapacite > 0 ? round(($totalEleves / $totalCapacite) * 100, 1) : 0;

        // Ratio élèves/enseignant
        $totalEnseignants = User::whereHas('role', fn($q) => $q->where('name', 'enseignant'))->count();
        $ratioElevesEnseignant = $totalEnseignants > 0 ? round($totalEleves / $totalEnseignants, 1) : 0;

        return [
            'taux_remplissage' => $tauxRemplissage,
            'ratio_eleves_enseignant' => $ratioElevesEnseignant,
            'moyenne_eleves_par_classe' => Classe::count() > 0 ? round($totalEleves / Classe::count(), 1) : 0,
            'moyenne_classes_par_etablissement' => Etablissement::count() > 0 
                ? round(Classe::count() / Etablissement::count(), 1)
                : 0,
        ];
    }

    /**
     * Export des statistiques
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $type = $request->get('type', 'general');

        switch ($format) {
            case 'pdf':
                return $this->exportPdf($type);
            case 'excel':
                return $this->exportExcel($type);
            default:
                return redirect()->back()->with('error', 'Format non supporté');
        }
    }

    /**
     * Export PDF - Version CORRIGÉE avec toutes les statistiques
     */
    private function exportPdf($type)
    {
        try {
            //Récupérer TOUTES les statistiques, pas seulement les générales
            $stats = [
                'general' => $this->getGeneralStats(),
                'evolution' => $this->getEvolutionStats(),
                'repartition' => $this->getRepartitionStats(),
                'activite' => $this->getActiviteStats(),
                'top' => $this->getTopStats(),
                'performance' => $this->getPerformanceStats(),
            ];
            
            // Vérifier que la vue existe
            if (!view()->exists('admin.statistiques.pdf')) {
                return redirect()->back()->with('error', 'La vue PDF n\'existe pas');
            }
            
            // Générer le PDF avec stream() pour afficher dans le navigateur
            return Pdf::loadView('admin.statistiques.pdf', compact('stats', 'type'))
                      ->stream('statistiques-syscol-' . now()->format('Y-m-d') . '.pdf');
                      
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    /**
     * Export Excel (placeholder)
     */
    private function exportExcel($type)
    {
        // À implémenter avec une bibliothèque Excel
        return redirect()->back()->with('info', 'Export Excel en cours de développement');
    }

    /**
     * API pour les graphiques (AJAX)
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'evolution');

        switch ($type) {
            case 'evolution':
                return response()->json($this->getEvolutionStats());
            case 'repartition':
                return response()->json($this->getRepartitionStats());
            default:
                return response()->json(['error' => 'Type non reconnu'], 400);
        }
    }
}