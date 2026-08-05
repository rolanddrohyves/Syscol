<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Depense;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RapportFinancierController extends Controller
{
    /**
     * Affiche la page des rapports financiers
     */
    public function index()
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        // Statistiques générales
        $totalPaiements = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->sum('montant') ?? 0;
        
        $totalDepenses = Depense::where('etablissement_id', $etablissementId)->sum('montant') ?? 0;
        
        $solde = $totalPaiements - $totalDepenses;
        
        // Paiements par mois
        $paiementsParMois = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->selectRaw('YEAR(date_paiement) as annee, MONTH(date_paiement) as mois, SUM(montant) as total')
            ->groupBy('annee', 'mois')
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->get();
        
        // Paiements par mode
        $paiementsParMode = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->selectRaw('mode_paiement, COUNT(*) as nombre, SUM(montant) as total')
            ->groupBy('mode_paiement')
            ->get();
        
        // Paiements par classe
        $paiementsParClasse = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with('eleve.classe')
            ->get()
            ->groupBy('eleve.classe.nom')
            ->map(function($items) {
                return [
                    'total' => $items->sum('montant'),
                    'nombre' => $items->count()
                ];
            });
        
        // Top 10 des élèves payeurs
        $topEleves = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with('eleve')
            ->selectRaw('eleve_id, SUM(montant) as total')
            ->groupBy('eleve_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();
        
        // Impayés
        $totalImpayes = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->sum('montant_restant') ?? 0;
        
        $nombreImpayes = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })->where('montant_restant', '>', 0)->count();
        
        return view('comptable.rapports.index', compact(
            'totalPaiements',
            'totalDepenses',
            'solde',
            'paiementsParMois',
            'paiementsParMode',
            'paiementsParClasse',
            'topEleves',
            'totalImpayes',
            'nombreImpayes'
        ));
    }
    
    /**
     * Génère un rapport journalier
     */
    public function journalier(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        $date = $request->get('date', Carbon::now()->toDateString());
        
        $paiements = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereDate('date_paiement', $date)
            ->with(['eleve', 'frais'])
            ->get();
        
        $depenses = Depense::where('etablissement_id', $etablissementId)
            ->whereDate('date', $date)
            ->get();
        
        $totalPaiements = $paiements->sum('montant');
        $totalDepenses = $depenses->sum('montant');
        
        return view('comptable.rapports.journalier', compact('paiements', 'depenses', 'totalPaiements', 'totalDepenses', 'date'));
    }
    
    /**
     * Génère un rapport mensuel
     */
    public function mensuel(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        $mois = $request->get('mois', Carbon::now()->month);
        $annee = $request->get('annee', Carbon::now()->year);
        
        $paiements = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereMonth('date_paiement', $mois)
            ->whereYear('date_paiement', $annee)
            ->with(['eleve', 'frais'])
            ->get();
        
        $depenses = Depense::where('etablissement_id', $etablissementId)
            ->whereMonth('date', $mois)
            ->whereYear('date', $annee)
            ->get();
        
        $totalPaiements = $paiements->sum('montant');
        $totalDepenses = $depenses->sum('montant');
        
        $nomMois = Carbon::createFromDate($annee, $mois, 1)->locale('fr')->monthName;
        
        return view('comptable.rapports.mensuel', compact('paiements', 'depenses', 'totalPaiements', 'totalDepenses', 'mois', 'annee', 'nomMois'));
    }
    
    /**
     * Génère un rapport annuel
     */
    public function annuel(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        $annee = $request->get('annee', Carbon::now()->year);
        
        $paiementsParMois = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereYear('date_paiement', $annee)
            ->selectRaw('MONTH(date_paiement) as mois, SUM(montant) as total')
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
        
        $depensesParMois = Depense::where('etablissement_id', $etablissementId)
            ->whereYear('date', $annee)
            ->selectRaw('MONTH(date) as mois, SUM(montant) as total')
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
        
        $totalPaiements = $paiementsParMois->sum('total');
        $totalDepenses = $depensesParMois->sum('total');
        
        return view('comptable.rapports.annuel', compact('paiementsParMois', 'depensesParMois', 'totalPaiements', 'totalDepenses', 'annee'));
    }
    
    /**
     * Export du rapport au format CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        $type = $request->get('type', 'journalier');
        
        if ($type == 'journalier') {
            return $this->exportJournalier($request, $etablissementId);
        } elseif ($type == 'mensuel') {
            return $this->exportMensuel($request, $etablissementId);
        } else {
            return $this->exportAnnuel($request, $etablissementId);
        }
    }
    
    /**
     * Export journalier CSV
     */
    private function exportJournalier($request, $etablissementId)
    {
        $date = $request->get('date', Carbon::now()->toDateString());
        
        $paiements = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereDate('date_paiement', $date)
            ->with(['eleve', 'frais'])
            ->get();
        
        $depenses = Depense::where('etablissement_id', $etablissementId)
            ->whereDate('date', $date)
            ->get();
        
        $filename = 'rapport_journalier_' . $date . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($handle, ['RAPPORT JOURNALIER DU ' . date('d/m/Y', strtotime($date))]);
        fputcsv($handle, []);
        fputcsv($handle, ['PAIEMENTS']);
        fputcsv($handle, ['Date', 'Élève', 'Classe', 'Frais', 'Montant', 'Mode']);
        
        foreach ($paiements as $p) {
            fputcsv($handle, [
                $p->date_paiement->format('d/m/Y H:i'),
                $p->eleve->prenom . ' ' . $p->eleve->nom,
                $p->eleve->classe->nom ?? '',
                $p->frais->libelle ?? '',
                number_format($p->montant, 0, ',', ' '),
                $p->mode_paiement
            ]);
        }
        
        fputcsv($handle, []);
        fputcsv($handle, ['Total paiements', '', '', '', number_format($paiements->sum('montant'), 0, ',', ' '), '']);
        fputcsv($handle, []);
        fputcsv($handle, ['DÉPENSES']);
        fputcsv($handle, ['Date', 'Libellé', 'Catégorie', 'Montant', '']);
        
        foreach ($depenses as $d) {
            fputcsv($handle, [
                $d->date->format('d/m/Y H:i'),
                $d->libelle,
                $d->categorie ?? '',
                number_format($d->montant, 0, ',', ' '),
                ''
            ]);
        }
        
        fputcsv($handle, []);
        fputcsv($handle, ['Total dépenses', '', '', number_format($depenses->sum('montant'), 0, ',', ' '), '']);
        fputcsv($handle, []);
        fputcsv($handle, ['SOLDE', '', '', number_format($paiements->sum('montant') - $depenses->sum('montant'), 0, ',', ' '), '']);
        
        fclose($handle);
        exit;
    }
    
    /**
     * Export mensuel CSV
     */
    private function exportMensuel($request, $etablissementId)
    {
        $mois = $request->get('mois', Carbon::now()->month);
        $annee = $request->get('annee', Carbon::now()->year);
        
        $paiements = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereMonth('date_paiement', $mois)
            ->whereYear('date_paiement', $annee)
            ->get();
        
        $depenses = Depense::where('etablissement_id', $etablissementId)
            ->whereMonth('date', $mois)
            ->whereYear('date', $annee)
            ->get();
        
        $nomMois = Carbon::createFromDate($annee, $mois, 1)->locale('fr')->monthName;
        $filename = 'rapport_mensuel_' . $nomMois . '_' . $annee . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($handle, ['RAPPORT MENSUEL - ' . strtoupper($nomMois) . ' ' . $annee]);
        fputcsv($handle, []);
        fputcsv($handle, ['Total paiements', number_format($paiements->sum('montant'), 0, ',', ' ') . ' FCFA']);
        fputcsv($handle, ['Total dépenses', number_format($depenses->sum('montant'), 0, ',', ' ') . ' FCFA']);
        fputcsv($handle, ['Solde', number_format($paiements->sum('montant') - $depenses->sum('montant'), 0, ',', ' ') . ' FCFA']);
        
        fclose($handle);
        exit;
    }
    
    /**
     * Export annuel CSV
     */
    private function exportAnnuel($request, $etablissementId)
    {
        $annee = $request->get('annee', Carbon::now()->year);
        
        $paiements = Paiement::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereYear('date_paiement', $annee)
            ->get();
        
        $depenses = Depense::where('etablissement_id', $etablissementId)
            ->whereYear('date', $annee)
            ->get();
        
        $filename = 'rapport_annuel_' . $annee . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($handle, ['RAPPORT ANNUEL ' . $annee]);
        fputcsv($handle, []);
        fputcsv($handle, ['Total paiements', number_format($paiements->sum('montant'), 0, ',', ' ') . ' FCFA']);
        fputcsv($handle, ['Total dépenses', number_format($depenses->sum('montant'), 0, ',', ' ') . ' FCFA']);
        fputcsv($handle, ['Solde', number_format($paiements->sum('montant') - $depenses->sum('montant'), 0, ',', ' ') . ' FCFA']);
        
        fclose($handle);
        exit;
    }
}