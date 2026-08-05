<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Etablissement;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administrateur
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statistiques globales
        $stats = $this->getGlobalStats();
        
        // Derniers établissements créés
        $derniersEtablissements = Etablissement::latest()
            ->take(5)
            ->get();
        
        // Derniers utilisateurs inscrits avec leurs relations
        $derniersUtilisateurs = User::with(['role', 'etablissement'])
            ->latest()
            ->take(5)
            ->get();
        
        // Répartition par type d'établissement
        $repartitionEtablissements = Etablissement::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();
        
        // Évolution des inscriptions (simulée pour le moment)
        $evolutionInscriptions = $this->getEvolutionInscriptions();
        
        // Activités récentes (à remplacer par de vraies données)
        $activites = $this->getActivitesRecentes();
        
        // Alertes et notifications
        $alertes = $this->getAlertes();
        
        return view('admin.dashboard', compact(
            'stats',
            'derniersEtablissements',
            'derniersUtilisateurs',
            'repartitionEtablissements',
            'evolutionInscriptions',
            'activites',
            'alertes'
        ));
    }

    /**
     * Récupère les statistiques globales
     */
    private function getGlobalStats()
    {
        return [
            'etablissements' => Etablissement::count(),
            'utilisateurs' => User::count(),
            'eleves' => Eleve::count(),
            'enseignants' => User::whereHas('role', function($q) {
                $q->where('name', 'enseignant');
            })->count(),
            'admins' => User::whereHas('role', function($q) {
                $q->whereIn('name', ['super_admin', 'admin_etablissement']);
            })->count(),
            'classes' => \App\Models\Classe::count(),
        ];
    }

    /**
     * Récupère l'évolution des inscriptions
     */
    private function getEvolutionInscriptions()
    {
        // Exemple de données pour le graphique
        $mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'];
        $inscriptions = [45, 52, 48, 70, 65, 80];
        
        return [
            'mois' => $mois,
            'inscriptions' => $inscriptions,
        ];
    }

    /**
     * Récupère les activités récentes
     */
    private function getActivitesRecentes()
    {
        // À remplacer par de vraies données venant d'une table d'activités
        return [
            [
                'icon' => 'school',
                'color' => 'indigo',
                'message' => 'Nouvel établissement ajouté : Lycée Technique',
                'time' => 'il y a 5 minutes',
            ],
            [
                'icon' => 'user-graduate',
                'color' => 'green',
                'message' => '15 nouveaux élèves inscrits',
                'time' => 'il y a 2 heures',
            ],
            [
                'icon' => 'chalkboard-teacher',
                'color' => 'purple',
                'message' => '3 nouveaux enseignants enregistrés',
                'time' => 'hier',
            ],
            [
                'icon' => 'cog',
                'color' => 'blue',
                'message' => 'Mise à jour du système effectuée',
                'time' => 'hier',
            ],
            [
                'icon' => 'database',
                'color' => 'yellow',
                'message' => 'Sauvegarde automatique terminée',
                'time' => 'il y a 3 jours',
            ],
        ];
    }

    /**
     * Récupère les alertes
     */
    private function getAlertes()
    {
        $alertes = [];
        
        // Vérifier les établissements sans admin
        $etablissementsSansAdmin = Etablissement::whereDoesntHave('users', function($q) {
            $q->whereHas('role', function($r) {
                $r->where('name', 'admin_etablissement');
            });
        })->count();
        
        if ($etablissementsSansAdmin > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "{$etablissementsSansAdmin} établissement(s) sans administrateur",
            ];
        }
        
        // Vérifier les classes sans professeur principal
        $classesSansPP = \App\Models\Classe::whereNull('professeur_principal_id')->count();
        
        if ($classesSansPP > 0) {
            $alertes[] = [
                'type' => 'info',
                'message' => "{$classesSansPP} classe(s) sans professeur principal",
            ];
        }
        
        return $alertes;
    }

    /**
     * Exporte les statistiques (optionnel)
     */
    public function export()
    {
        // Logique d'export
        return response()->json([
            'success' => true,
            'message' => 'Export en cours de développement',
        ]);
    }

    /**
     * Rafraîchit les données du dashboard (pour AJAX)
     */
    public function refresh()
    {
        return response()->json([
            'stats' => $this->getGlobalStats(),
            'alertes' => $this->getAlertes(),
        ]);
    }
}