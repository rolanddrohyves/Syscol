<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\EmploiTemps;
use App\Models\Presence;
use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\AnneeScolaire;
use App\Models\Trimestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PresenceController extends Controller
{
    /**
     * Affiche la page de gestion des présences
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Récupérer les classes de l'enseignant
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->get();
        
        // Récupérer la classe sélectionnée
        $classeId = $request->get('classe_id');
        $date = $request->get('date', Carbon::today()->toDateString());
        
        $classe = null;
        $eleves = collect();
        $presencesExistantes = collect();
        
        if ($classeId) {
            $classe = Classe::with('eleves')->find($classeId);
            $eleves = $classe->eleves ?? collect();
            
            // Récupérer les présences existantes pour cette date
            $presencesExistantes = Presence::where('classe_id', $classeId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('eleve_id');
        }
        
        // Récupérer l'année scolaire et le trimestre actuels
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->where('is_current', true)
            ->first();
        
        $trimestreActuel = null;
        if ($anneeScolaire) {
            $trimestreActuel = Trimestre::where('annee_scolaire_id', $anneeScolaire->id)
                ->where('is_current', true)
                ->first();
        }
        
        return view('enseignant.presences.index', compact(
            'classes', 'classe', 'eleves', 'presencesExistantes', 
            'date', 'classeId', 'anneeScolaire', 'trimestreActuel'
        ));
    }
    
    /**
     * Enregistre les présences pour une classe et une date
     */
    public function marquer(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        $etablissementId = $user->etablissement_id;
        
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'date' => 'required|date',
            'presences' => 'required|array',
            'presences.*.eleve_id' => 'required|exists:eleves,id',
            'presences.*.statut' => 'required|in:present,absent,retard,excuse',
        ]);
        
        $classeId = $request->classe_id;
        $date = $request->date;
        
        // Récupérer l'année scolaire et le trimestre
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->where('is_current', true)
            ->first();
        
        $trimestreActuel = null;
        if ($anneeScolaire) {
            $trimestreActuel = Trimestre::where('annee_scolaire_id', $anneeScolaire->id)
                ->where('is_current', true)
                ->first();
        }
        
        foreach ($request->presences as $presenceData) {
            Presence::updateOrCreate(
                [
                    'eleve_id' => $presenceData['eleve_id'],
                    'classe_id' => $classeId,
                    'date' => $date,
                ],
                [
                    'statut' => $presenceData['statut'],
                    'justifiee' => $presenceData['statut'] == 'excuse',
                    'motif' => $presenceData['motif'] ?? null,
                    'enseignant_id' => $enseignantId,
                    'annee_scolaire_id' => $anneeScolaire?->id,
                    'trimestre_id' => $trimestreActuel?->id,
                    'heure_arrivee' => $presenceData['statut'] == 'retard' ? Carbon::now() : null,
                ]
            );
        }
        
        return redirect()->route('enseignant.presences', ['classe_id' => $classeId, 'date' => $date])
            ->with('success', 'Les présences ont été enregistrées avec succès.');
    }
    
    /**
     * Exporte les présences d'une classe en CSV
     */
    public function export(Request $request, $classeId)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Vérifier que l'enseignant a accès à cette classe
        $aAcces = EmploiTemps::where('enseignant_id', $enseignantId)
            ->where('classe_id', $classeId)
            ->exists();
        
        if (!$aAcces) {
            return redirect()->route('enseignant.presences')
                ->with('error', 'Vous n\'avez pas accès à cette classe.');
        }
        
        $classe = Classe::findOrFail($classeId);
        $dateDebut = $request->get('date_debut', Carbon::now()->startOfMonth()->toDateString());
        $dateFin = $request->get('date_fin', Carbon::now()->endOfMonth()->toDateString());
        
        $presences = Presence::where('classe_id', $classeId)
            ->whereBetween('date', [$dateDebut, $dateFin])
            ->with(['eleve'])
            ->orderBy('date')
            ->get();
        
        // Grouper par élève
        $presencesParEleve = $presences->groupBy('eleve_id');
        
        $filename = 'presences_' . $classe->nom . '_' . $dateDebut . '_' . $dateFin . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // En-tête
        $jours = [];
        $dateCourante = Carbon::parse($dateDebut);
        while ($dateCourante <= Carbon::parse($dateFin)) {
            $jours[] = $dateCourante->format('d/m');
            $dateCourante->addDay();
        }
        
        fputcsv($handle, array_merge(['N°', 'Matricule', 'Nom', 'Prénom'], $jours, ['Total Présences', 'Total Absences', 'Total Retards', 'Taux %']));
        
        // Données
        foreach ($classe->eleves as $index => $eleve) {
            $row = [
                $index + 1,
                $eleve->matricule ?? '',
                $eleve->nom,
                $eleve->prenom,
            ];
            
            $totalPresences = 0;
            $totalAbsences = 0;
            $totalRetards = 0;
            
            $dateCourante = Carbon::parse($dateDebut);
            while ($dateCourante <= Carbon::parse($dateFin)) {
                $presence = $presences->firstWhere(function($p) use ($eleve, $dateCourante) {
                    return $p->eleve_id == $eleve->id && $p->date == $dateCourante->toDateString();
                });
                
                if ($presence) {
                    $statut = $presence->statut;
                    if ($statut == 'present') {
                        $totalPresences++;
                        $row[] = 'P';
                    } elseif ($statut == 'absent') {
                        $totalAbsences++;
                        $row[] = 'A';
                    } elseif ($statut == 'retard') {
                        $totalRetards++;
                        $row[] = 'R';
                    } elseif ($statut == 'excuse') {
                        $row[] = 'E';
                    } else {
                        $row[] = '-';
                    }
                } else {
                    $row[] = '-';
                }
                
                $dateCourante->addDay();
            }
            
            $totalJours = count($jours);
            $taux = $totalJours > 0 ? round(($totalPresences / $totalJours) * 100, 2) : 0;
            
            $row[] = $totalPresences;
            $row[] = $totalAbsences;
            $row[] = $totalRetards;
            $row[] = $taux . '%';
            
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        exit;
    }
    
    /**
     * Récupère les statistiques de présence pour une classe
     */
    public function statistiques(Request $request, $classeId)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return response()->json(['error' => 'Enseignant non trouvé'], 404);
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Vérifier que l'enseignant a accès à cette classe
        $aAcces = EmploiTemps::where('enseignant_id', $enseignantId)
            ->where('classe_id', $classeId)
            ->exists();
        
        if (!$aAcces) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }
        
        $periode = $request->get('periode', 'mois'); // semaine, mois, trimestre, annee
        
        $dateDebut = Carbon::now();
        $dateFin = Carbon::now();
        
        switch ($periode) {
            case 'semaine':
                $dateDebut = Carbon::now()->startOfWeek();
                $dateFin = Carbon::now()->endOfWeek();
                break;
            case 'mois':
                $dateDebut = Carbon::now()->startOfMonth();
                $dateFin = Carbon::now()->endOfMonth();
                break;
            case 'trimestre':
                $dateDebut = Carbon::now()->startOfQuarter();
                $dateFin = Carbon::now()->endOfQuarter();
                break;
            case 'annee':
                $dateDebut = Carbon::now()->startOfYear();
                $dateFin = Carbon::now()->endOfYear();
                break;
        }
        
        $stats = [
            'total_presents' => Presence::where('classe_id', $classeId)
                ->whereBetween('date', [$dateDebut, $dateFin])
                ->where('statut', 'present')
                ->count(),
            'total_absents' => Presence::where('classe_id', $classeId)
                ->whereBetween('date', [$dateDebut, $dateFin])
                ->where('statut', 'absent')
                ->count(),
            'total_retards' => Presence::where('classe_id', $classeId)
                ->whereBetween('date', [$dateDebut, $dateFin])
                ->where('statut', 'retard')
                ->count(),
            'total_excuses' => Presence::where('classe_id', $classeId)
                ->whereBetween('date', [$dateDebut, $dateFin])
                ->where('statut', 'excuse')
                ->count(),
        ];
        
        $total = $stats['total_presents'] + $stats['total_absents'] + $stats['total_retards'];
        $stats['taux_presence'] = $total > 0 ? round(($stats['total_presents'] / $total) * 100, 2) : 0;
        
        return response()->json($stats);
    }
}