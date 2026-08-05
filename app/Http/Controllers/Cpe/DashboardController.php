<?php
// app/Http/Controllers/Cpe/DashboardController.php

namespace App\Http\Controllers\Cpe;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\Sanction;
use App\Models\AnneeScolaire;
use App\Models\Trimestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord du CPE
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
        
        // Statistiques générales
        $totalEleves = Eleve::whereHas('classe', function($q) use ($etablissementId) {
            $q->where('etablissement_id', $etablissementId);
        })->count();
        
        $totalClasses = Classe::where('etablissement_id', $etablissementId)->count();
        
        $stats = [
            'total_eleves' => $totalEleves,
            'total_classes' => $totalClasses,
            'total_sanctions' => Sanction::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count(),
            'sanctions_en_cours' => Sanction::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('statut', 'en_cours')
                ->count(),
            'presences_aujourdhui' => $this->calculerTauxPresence($etablissementId),
        ];
        
        // Statistiques des absences
        $statsAbsences = [
            'aujourdhui' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId);
                })
                ->whereDate('date', Carbon::today())
                ->count(),
            'cette_semaine' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId);
                })
                ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'ce_mois' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId);
                })
                ->whereMonth('date', Carbon::now()->month)
                ->count(),
            'non_justifiees' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId);
                })
                ->where('justifiee', false)
                ->count(),
        ];
        
        // Statistiques des retards
        $statsRetards = [
            'aujourdhui' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId);
                })
                ->where('type', 'retard')
                ->whereDate('date', Carbon::today())
                ->count(),
            'cette_semaine' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId);
                })
                ->where('type', 'retard')
                ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'ce_mois' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                    $q->where('etablissement_id', $etablissementId);
                })
                ->where('type', 'retard')
                ->whereMonth('date', Carbon::now()->month)
                ->count(),
        ];
        
        // Récupération des classes avec leurs statistiques détaillées
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $absencesParClasse = [];
        foreach ($classes as $classe) {
            // Compter les élèves actifs de la classe
            $totalElevesClasse = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->count();
            
            // Absences du mois
            $absencesMois = Absence::whereHas('eleve', function($q) use ($classe) {
                    $q->where('classe_id', $classe->id);
                })
                ->whereMonth('date', Carbon::now()->month)
                ->count();
            
            // Retards du mois
            $retardsMois = Absence::whereHas('eleve', function($q) use ($classe) {
                    $q->where('classe_id', $classe->id);
                })
                ->where('type', 'retard')
                ->whereMonth('date', Carbon::now()->month)
                ->count();
            
            // Sanctions totales
            $sanctionsCount = Sanction::whereHas('eleve', function($q) use ($classe) {
                    $q->where('classe_id', $classe->id);
                })
                ->count();
            
            // Absences aujourd'hui
            $absencesAujourdhui = Absence::whereHas('eleve', function($q) use ($classe) {
                    $q->where('classe_id', $classe->id);
                })
                ->whereDate('date', Carbon::today())
                ->count();
            
            // Calcul du taux de présence
            $tauxPresence = $totalElevesClasse > 0 
                ? round((($totalElevesClasse - $absencesAujourdhui) / $totalElevesClasse) * 100, 1)
                : 0;
            
            $absencesParClasse[] = (object)[
                'id' => $classe->id,
                'nom' => $classe->nom,
                'niveau' => $classe->niveau,
                'eleves_count' => $totalElevesClasse,
                'absences_mois' => $absencesMois,
                'retards_mois' => $retardsMois,
                'sanctions_count' => $sanctionsCount,
                'absences_aujourdhui' => $absencesAujourdhui,
                'taux_presence' => $tauxPresence,
                'eleves_avec_absences' => Eleve::where('classe_id', $classe->id)
                    ->whereHas('absences', function($q) {
                        $q->whereMonth('date', Carbon::now()->month);
                    })
                    ->count(),
            ];
        }
        
        // Graphique des absences (7 derniers jours)
        $absences7Jours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $absences7Jours[] = [
                'date' => $date->format('d/m'),
                'total' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                        $q->where('etablissement_id', $etablissementId);
                    })
                    ->whereDate('date', $date)
                    ->count(),
            ];
        }
        
        // Graphique des retards (7 derniers jours)
        $retards7Jours = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $retards7Jours[] = [
                'date' => $date->format('d/m'),
                'total' => Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                        $q->where('etablissement_id', $etablissementId);
                    })
                    ->where('type', 'retard')
                    ->whereDate('date', $date)
                    ->count(),
            ];
        }
        
        // Dernières absences
        $dernieresAbsences = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Derniers retards
        $derniersRetards = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('type', 'retard')
            ->with(['eleve', 'eleve.classe'])
            ->orderBy('date', 'desc')
            ->orderBy('heure', 'desc')
            ->limit(10)
            ->get();
        
        // Dernières sanctions
        $dernieresSanctions = Sanction::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe'])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        // Élèves les plus absents
        $elevesPlusAbsents = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with('classe')
            ->withCount(['absences' => function($q) {
                $q->where('type', 'absence');
            }])
            ->orderBy('absences_count', 'desc')
            ->limit(5)
            ->get();
        
        // Élèves avec le plus de retards
        $elevesPlusRetards = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with('classe')
            ->withCount(['absences' => function($q) {
                $q->where('type', 'retard');
            }])
            ->orderBy('absences_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('cpe.dashboard', compact(
            'stats',
            'statsAbsences',
            'statsRetards',
            'absences7Jours',
            'retards7Jours',
            'dernieresAbsences',
            'derniersRetards',
            'dernieresSanctions',
            'elevesPlusAbsents',
            'elevesPlusRetards',
            'absencesParClasse',
            'anneeEnCours',
            'trimestreEnCours'
        ));
    }

    /**
     * Calcule le taux de présence du jour
     */
    private function calculerTauxPresence($etablissementId)
    {
        $totalEleves = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('status', 'actif')
            ->count();
        
        if ($totalEleves == 0) return 0;
        
        $absencesAujourdhui = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->whereDate('date', Carbon::today())
            ->count();
        
        $present = $totalEleves - $absencesAujourdhui;
        return round(($present / $totalEleves) * 100);
    }

    /**
     * Affiche la liste des absences
     */
    public function absences(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe']);
        
        // Filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        if ($request->filled('justifiee')) {
            $query->where('justifiee', $request->justifiee);
        }
        
        $absences = $query->orderBy('date', 'desc')->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        return view('cpe.absences.index', compact('absences', 'classes'));
    }

    /**
     * Affiche la liste des retards
     */
    public function retards(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->where('type', 'retard')
            ->with(['eleve', 'eleve.classe']);
        
        // Filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', function($q) use ($request) {
                $q->where('classe_id', $request->classe_id);
            });
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }
        
        $retards = $query->orderBy('date', 'desc')
            ->orderBy('heure', 'desc')
            ->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        
        return view('cpe.retards.index', compact('retards', 'classes'));
    }

    /**
     * Justifie une absence
     */
    public function justifierAbsence(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        
        $request->validate([
            'justification' => 'required|string|max:500',
        ]);
        
        $absence->update([
            'justifiee' => true,
            'motif' => $request->justification,
        ]);
        
        return redirect()->back()->with('success', 'Absence justifiée avec succès.');
    }

    /**
     * Statistiques détaillées
     */
    public function statistiques()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        // Statistiques par classe
        $statsParClasse = Classe::where('etablissement_id', $etablissementId)
            ->withCount(['eleves'])
            ->with(['eleves' => function($q) {
                $q->withCount(['absences']);
            }])
            ->get()
            ->map(function($classe) {
                return [
                    'nom' => $classe->nom,
                    'eleves' => $classe->eleves_count,
                    'absences' => $classe->eleves->sum('absences_count'),
                    'retards' => $classe->eleves->flatMap->absences->where('type', 'retard')->count(),
                    'moyenne_absences' => $classe->eleves_count > 0 
                        ? round($classe->eleves->sum('absences_count') / $classe->eleves_count, 1)
                        : 0,
                ];
            });
        
        return response()->json([
            'par_classe' => $statsParClasse,
        ]);
    }

    /**
     * Export des données
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        $type = $request->get('type', 'absences');
        
        $filename = $type . '_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // BOM UTF-8 pour Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        switch ($type) {
            case 'absences':
                fputcsv($handle, ['Date', 'Jour', 'Élève', 'Classe', 'Justifiée', 'Motif']);
                
                $absences = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                        $q->where('etablissement_id', $etablissementId);
                    })
                    ->with(['eleve', 'eleve.classe'])
                    ->orderBy('date', 'desc')
                    ->get();
                
                foreach ($absences as $absence) {
                    fputcsv($handle, [
                        $absence->date->format('d/m/Y'),
                        $absence->date->locale('fr')->dayName,
                        $absence->eleve->prenom . ' ' . $absence->eleve->nom,
                        $absence->eleve->classe->nom,
                        $absence->justifiee ? 'Oui' : 'Non',
                        $absence->motif ?? '',
                    ]);
                }
                break;
                
            case 'retards':
                fputcsv($handle, ['Date', 'Heure', 'Élève', 'Classe', 'Motif']);
                
                $retards = Absence::whereHas('eleve.classe', function($q) use ($etablissementId) {
                        $q->where('etablissement_id', $etablissementId);
                    })
                    ->where('type', 'retard')
                    ->with(['eleve', 'eleve.classe'])
                    ->orderBy('date', 'desc')
                    ->get();
                
                foreach ($retards as $retard) {
                    fputcsv($handle, [
                        $retard->date->format('d/m/Y'),
                        $retard->heure ?? 'N/A',
                        $retard->eleve->prenom . ' ' . $retard->eleve->nom,
                        $retard->eleve->classe->nom,
                        $retard->motif ?? '',
                    ]);
                }
                break;
                
            case 'sanctions':
                fputcsv($handle, ['Date', 'Élève', 'Classe', 'Type', 'Motif', 'Statut']);
                
                $sanctions = Sanction::whereHas('eleve.classe', function($q) use ($etablissementId) {
                        $q->where('etablissement_id', $etablissementId);
                    })
                    ->with(['eleve', 'eleve.classe'])
                    ->orderBy('date', 'desc')
                    ->get();
                
                foreach ($sanctions as $sanction) {
                    fputcsv($handle, [
                        $sanction->date->format('d/m/Y'),
                        $sanction->eleve->prenom . ' ' . $sanction->eleve->nom,
                        $sanction->eleve->classe->nom,
                        $sanction->type,
                        $sanction->motif,
                        $sanction->statut,
                    ]);
                }
                break;
        }
        
        fclose($handle);
        exit;
    }
}