<?php
// app/Http/Controllers/Enseignant/RapportController.php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Enseignant;
use App\Models\Note;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RapportController extends Controller
{
    /**
     * Affiche la page des rapports
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Période par défaut: mois en cours
        $periode = $request->get('periode', 'mois');
        $classeId = $request->get('classe_id');
        $matiereId = $request->get('matiere_id');
        
        // Récupérer les dates
        switch ($periode) {
            case 'semaine':
                $dateDebut = Carbon::now()->startOfWeek();
                $dateFin = Carbon::now()->endOfWeek();
                break;
            case 'trimestre':
                $dateDebut = Carbon::now()->startOfQuarter();
                $dateFin = Carbon::now()->endOfQuarter();
                break;
            case 'annee':
                $dateDebut = Carbon::now()->startOfYear();
                $dateFin = Carbon::now()->endOfYear();
                break;
            default: // mois
                $dateDebut = Carbon::now()->startOfMonth();
                $dateFin = Carbon::now()->endOfMonth();
                break;
        }
        
        // Récupérer les classes et matières pour les filtres
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->get();
        
        $matieres = $enseignant->matieres;
        
        // Construction de la requête des notes - CORRECTION : pas de 'evaluation'
        $queryNotes = Note::where('enseignant_id', $enseignantId);
        
        if ($classeId) {
            $queryNotes->whereHas('eleve', function($q) use ($classeId) {
                $q->where('classe_id', $classeId);
            });
        }
        
        if ($matiereId) {
            $queryNotes->where('matiere_id', $matiereId);
        }
        
        if ($request->filled('date_debut')) {
            $queryNotes->whereDate('date_evaluation', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $queryNotes->whereDate('date_evaluation', '<=', $request->date_fin);
        }
        
        $notes = $queryNotes->get();
        
        // Statistiques générales
        $stats = [
            'total_notes' => $notes->count(),
            'moyenne_generale' => round($notes->avg('note') ?: 0, 2),
            'meilleure_note' => round($notes->max('note') ?: 0, 2),
            'plus_faible_note' => round($notes->min('note') ?: 0, 2),
            'taux_reussite' => $notes->count() > 0 
                ? round(($notes->where('note', '>=', 10)->count() / $notes->count()) * 100, 2) 
                : 0,
        ];
        
        // Distribution des notes
        $distribution = [
            'excellent' => $notes->where('note', '>=', 16)->count(),
            'tres_bien' => $notes->whereBetween('note', [14, 15.99])->count(),
            'bien' => $notes->whereBetween('note', [12, 13.99])->count(),
            'passable' => $notes->whereBetween('note', [10, 11.99])->count(),
            'insuffisant' => $notes->whereBetween('note', [8, 9.99])->count(),
            'faible' => $notes->where('note', '<', 8)->count(),
        ];
        
        // Performance par classe
        $performanceClasses = [];
        foreach ($classes as $classe) {
            if ($classeId && $classe->id != $classeId) continue;
            
            $moyenneClasse = Note::where('enseignant_id', $enseignantId)
                ->whereHas('eleve', function($q) use ($classe) {
                    $q->where('classe_id', $classe->id);
                })
                ->when($matiereId, function($q) use ($matiereId) {
                    $q->where('matiere_id', $matiereId);
                })
                ->avg('note');
            
            $performanceClasses[] = [
                'classe' => $classe->nom,
                'moyenne' => round($moyenneClasse ?: 0, 2),
                'total_eleves' => $classe->eleves->count(),
                'notes_saisies' => Note::where('enseignant_id', $enseignantId)
                    ->whereHas('eleve', function($q) use ($classe) {
                        $q->where('classe_id', $classe->id);
                    })
                    ->count(),
            ];
        }
        
        // Évolution hebdomadaire des notes (6 dernières semaines)
        $evolutionHebdo = [];
        for ($i = 5; $i >= 0; $i--) {
            $dateSemaine = Carbon::now()->subWeeks($i);
            $semaine = $dateSemaine->weekOfYear;
            
            $moyenne = Note::where('enseignant_id', $enseignantId)
                ->whereBetween('date_evaluation', [
                    $dateSemaine->copy()->startOfWeek(), 
                    $dateSemaine->copy()->endOfWeek()
                ])
                ->when($classeId, function($q) use ($classeId) {
                    $q->whereHas('eleve', function($sub) use ($classeId) {
                        $sub->where('classe_id', $classeId);
                    });
                })
                ->when($matiereId, function($q) use ($matiereId) {
                    $q->where('matiere_id', $matiereId);
                })
                ->avg('note');
            
            $evolutionHebdo[] = [
                'semaine' => 'S' . $semaine,
                'moyenne' => round($moyenne ?: 0, 2),
            ];
        }
        
        return view('enseignant.rapports.index', compact(
            'stats',
            'distribution',
            'performanceClasses',
            'evolutionHebdo',
            'classes',
            'matieres',
            'classeId',
            'matiereId',
            'periode',
            'dateDebut',
            'dateFin'
        ));
    }
    
    /**
     * Exporte le rapport au format CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        $classeId = $request->get('classe_id');
        $matiereId = $request->get('matiere_id');
        
        $query = Note::where('enseignant_id', $enseignantId)
            ->with(['eleve', 'matiere']);
        
        if ($classeId) {
            $query->whereHas('eleve', function($q) use ($classeId) {
                $q->where('classe_id', $classeId);
            });
        }
        
        if ($matiereId) {
            $query->where('matiere_id', $matiereId);
        }
        
        $notes = $query->get();
        
        if ($notes->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune donnée à exporter.');
        }
        
        $filename = 'rapport_notes_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($handle, ['Date', 'Élève', 'Matière', 'Note', 'Appréciation', 'Classe']);
        
        foreach ($notes as $note) {
            fputcsv($handle, [
                $note->date_evaluation ? Carbon::parse($note->date_evaluation)->format('d/m/Y') : '',
                $note->eleve->prenom . ' ' . $note->eleve->nom,
                $note->matiere->nom ?? '',
                $note->note . '/20',
                $note->appreciation ?? '',
                $note->eleve->classe->nom ?? '',
            ]);
        }
        
        fclose($handle);
        exit;
    }
}