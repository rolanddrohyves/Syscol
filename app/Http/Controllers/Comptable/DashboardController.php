<?php
// app/Http/Controllers/Comptable/DashboardController.php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\FraisScolarite;
use App\Models\Facture;
use App\Models\Depense;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\AnneeScolaire;
use App\Models\PaiementRelance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du comptable
     */
    public function index()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $anneeEnCours = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->where('is_current', true)
            ->first();
        
        // Statistiques
        $stats = $this->getStatistiques($etablissementId);
        
        // Derniers paiements - Filtrer par classe
        $derniersPaiements = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe', 'frais'])
            ->orderBy('date_paiement', 'desc')
            ->limit(10)
            ->get();
        
        // Prochains impayés
        $prochainsImpayes = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereIn('statut', ['en_attente', 'partiel'])
            ->with(['eleve', 'eleve.classe', 'frais'])
            ->orderBy('date_echeance')
            ->limit(5)
            ->get();
        
        // Alertes
        $alertes = $this->getAlertes($etablissementId);
        
        // Données pour les graphiques
        $evolutionPaiements = $this->getEvolutionPaiements($etablissementId);
        $repartitionFrais = $this->getRepartitionFrais($etablissementId);
        
        return view('comptable.dashboard', compact(
            'stats', 
            'derniersPaiements', 
            'prochainsImpayes',
            'alertes', 
            'anneeEnCours',
            'evolutionPaiements',
            'repartitionFrais'
        ));
    }

    /**
     * Récupère les statistiques financières
     */
    private function getStatistiques($etablissementId)
    {
        $now = Carbon::now();
        
        // Total paiements du mois
        $totalPaiementsMois = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereMonth('date_paiement', $now->month)
            ->whereYear('date_paiement', $now->year)
            ->sum('montant') ?? 0;
        
        // Total paiements de l'année
        $totalPaiementsAnnee = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereYear('date_paiement', $now->year)
            ->sum('montant') ?? 0;
        
        // Total dépenses du mois
        $totalDepensesMois = Depense::where('etablissement_id', $etablissementId)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('montant') ?? 0;
        
        // Nombre total d'impayés
        $totalImpayes = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereIn('statut', ['en_attente', 'partiel'])
            ->count();
        
        // Montant total des impayés
        $montantImpayes = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereIn('statut', ['en_attente', 'partiel'])
            ->get()
            ->sum(function($p) {
                return $p->montant - ($p->montant_paye ?? 0);
            });
        
        // Nombre de factures impayées
        $nombreImpayes = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('statut', 'en_attente')
            ->count();
        
        // Taux de recouvrement
        $tauxRecouvrement = $this->calculerTauxRecouvrement($etablissementId);
        
        // Nombre d'élèves avec impayés
        $nombreElevesImpayes = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereIn('statut', ['en_attente', 'partiel'])
            ->distinct('eleve_id')
            ->count('eleve_id');
        
        // Statistiques des relances
        $relancesEnCours = PaiementRelance::whereHas('paiement.eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('statut', 'en_attente')
            ->count();
        
        $relancesAujourdhui = PaiementRelance::whereHas('paiement.eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereDate('date_relance', $now->toDateString())
            ->count();
        
        $relancesAttente = PaiementRelance::whereHas('paiement.eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('statut', 'en_attente')
            ->count();
        
        return [
            'total_paiements_mois' => $totalPaiementsMois,
            'total_paiements_annee' => $totalPaiementsAnnee,
            'total_depenses_mois' => $totalDepensesMois,
            'total_impayes' => $nombreElevesImpayes,
            'montant_impayes' => $montantImpayes,
            'nombre_impayes' => $nombreImpayes,
            'taux_recouvrement' => $tauxRecouvrement,
            'relances_en_cours' => $relancesEnCours,
            'relances_aujourdhui' => $relancesAujourdhui,
            'relances_attente' => $relancesAttente,
            'total_attendu' => $this->getTotalAttendu($etablissementId)
        ];
    }

    /**
     * Calcule le total attendu pour l'année
     */
    private function getTotalAttendu($etablissementId)
    {
        // Compter les élèves via la classe
        $nbEleves = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->count();
        
        $fraisMoyen = FraisScolarite::where('etablissement_id', $etablissementId)
            ->avg('montant') ?? 0;
        
        return $nbEleves * $fraisMoyen;
    }

    /**
     * Calcule le taux de recouvrement
     */
    private function calculerTauxRecouvrement($etablissementId)
    {
        $totalAttendu = $this->getTotalAttendu($etablissementId);
        $totalPaye = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('statut', 'paye')
            ->sum('montant') ?? 0;
        
        if ($totalAttendu > 0) {
            return round(($totalPaye / $totalAttendu) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Récupère l'évolution des paiements
     */
    private function getEvolutionPaiements($etablissementId)
    {
        $months = collect(range(5, 0))->map(function($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });
        
        $paiements = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('date_paiement', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(date_paiement, "%Y-%m") as mois'),
                DB::raw('SUM(montant) as total')
            )
            ->groupBy('mois')
            ->pluck('total', 'mois');
        
        $labels = [];
        $donnees = [];
        
        foreach ($months as $month) {
            $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $donnees[] = $paiements[$month] ?? 0;
        }
        
        return [
            'labels' => $labels,
            'donnees' => $donnees
        ];
    }

    /**
     * Récupère la répartition des paiements par type de frais
     */
    private function getRepartitionFrais($etablissementId)
    {
        $repartition = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereYear('date_paiement', Carbon::now()->year)
            ->with('frais')
            ->get()
            ->groupBy('frais.nom')
            ->map(function($paiements) {
                return $paiements->sum('montant');
            });
        
        return [
            'labels' => $repartition->keys()->toArray(),
            'donnees' => $repartition->values()->toArray()
        ];
    }

    /**
     * Récupère les alertes
     */
    private function getAlertes($etablissementId)
    {
        $alertes = [];
        
        // Impayés de plus de 30 jours
        $impayesRetard = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereIn('statut', ['en_attente', 'partiel'])
            ->where('date_echeance', '<', Carbon::now()->subDays(30))
            ->count();
        
        if ($impayesRetard > 0) {
            $alertes[] = [
                'type' => 'danger',
                'message' => "{$impayesRetard} impayé(s) de plus de 30 jours",
                'lien' => route('comptable.impayes.index', ['statut_retard' => 'en_retard']),
            ];
        }
        
        // Échéances de la semaine
        $echeancesSemaine = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereIn('statut', ['en_attente', 'partiel'])
            ->whereBetween('date_echeance', [Carbon::now(), Carbon::now()->addDays(7)])
            ->count();
        
        if ($echeancesSemaine > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "{$echeancesSemaine} échéance(s) cette semaine",
                'lien' => route('comptable.impayes.index'),
            ];
        }
        
        return $alertes;
    }
}