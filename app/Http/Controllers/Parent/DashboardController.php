<?php
// app/Http/Controllers/Parent/DashboardController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Note;
use App\Models\Absence;
use App\Models\Paiement;
use App\Models\AnneeScolaire;
use App\Models\Trimestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        // Récupérer les enfants du parent connecté
        $enfants = Eleve::where('email_parent', $user->email)
            ->orWhere('telephone_parent', $user->telephone)
            ->with(['classe', 'notes.matiere', 'absences'])
            ->get();
        
        // Récupérer l'année scolaire en cours
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->where('is_current', true)
            ->first();
        
        if (!$anneeScolaire) {
            $anneeScolaire = AnneeScolaire::where('etablissement_id', $etablissementId)
                ->orderBy('id', 'desc')
                ->first();
        }
        
        // Récupérer le trimestre actuel
        $trimestreActuel = null;
        if ($anneeScolaire) {
            $trimestreActuel = Trimestre::where('annee_scolaire_id', $anneeScolaire->id)
                ->where('is_current', true)
                ->first();
        }
        
        // Statistiques globales
        $stats = [
            'total_enfants' => $enfants->count(),
            'total_absences' => 0,
            'total_retards' => 0,
            'moyenne_generale' => 0,
            'total_a_payer' => 0,
            'total_paye' => 0,
        ];
        
        $moyennesParEnfant = [];
        $absencesParEnfant = [];
        $dernieresNotes = collect();
        
        foreach ($enfants as $enfant) {
            // Moyennes par matière pour cet enfant
            $notes = Note::where('eleve_id', $enfant->id)
                ->whereNotNull('enseignant_id')
                ->with('matiere')
                ->get();
            
            $moyenneGenerale = $notes->avg('note') ?: 0;
            $stats['moyenne_generale'] += $moyenneGenerale;
            
            $moyennesParMatiere = [];
            foreach ($notes->groupBy('matiere_id') as $matiereId => $notesGroupe) {
                $matiere = $notesGroupe->first()->matiere;
                if ($matiere) {
                    $moyennesParMatiere[] = [
                        'matiere' => $matiere->nom,
                        'moyenne' => round($notesGroupe->avg('note'), 2),
                        'coefficient' => $matiere->coefficient ?? 1,
                    ];
                }
            }
            
            $moyennesParEnfant[$enfant->id] = [
                'enfant' => $enfant,
                'moyennes' => $moyennesParMatiere,
                'generale' => round($moyenneGenerale, 2),
            ];
            
            // Absences et retards
            $absences = Absence::where('eleve_id', $enfant->id)
                ->whereYear('date', now()->year)
                ->get();
            
            $totalAbsences = $absences->count();
            $totalRetards = $absences->where('est_retard', true)->count();
            
            $stats['total_absences'] += $totalAbsences;
            $stats['total_retards'] += $totalRetards;
            
            $absencesParEnfant[$enfant->id] = [
                'enfant' => $enfant,
                'total_absences' => $totalAbsences,
                'total_retards' => $totalRetards,
                'dernieres_absences' => $absences->sortByDesc('date')->take(3),
            ];
            
            // Dernières notes
            $dernieresNotes = $dernieresNotes->merge(
                Note::where('eleve_id', $enfant->id)
                    ->with(['matiere', 'eleve'])
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get()
            );
            
            // Paiements
            $paiements = Paiement::where('eleve_id', $enfant->id)->get();
            $stats['total_a_payer'] += $enfant->montant_total_frais ?? 0;
            $stats['total_paye'] += $paiements->sum('montant');
        }
        
        // Moyenne générale des enfants
        if ($stats['total_enfants'] > 0) {
            $stats['moyenne_generale'] = round($stats['moyenne_generale'] / $stats['total_enfants'], 2);
        }
        
        // Événements récents
        $evenementsRecents = $this->getEvenementsRecents($enfants);
        
        // Alertes
        $alertes = [];
        
        // Absences non justifiées
        $absencesNonJustifiees = Absence::whereIn('eleve_id', $enfants->pluck('id'))
            ->where('justifiee', false)
            ->whereDate('date', '>=', Carbon::now()->subDays(7))
            ->count();
        
        if ($absencesNonJustifiees > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "Vous avez {$absencesNonJustifiees} absence(s) non justifiée(s) cette semaine.",
                'lien' => route('parent.absences.index')
            ];
        }
        
        // Paiements en retard
        foreach ($enfants as $enfant) {
            $resteAPayer = ($enfant->montant_total_frais ?? 0) - Paiement::where('eleve_id', $enfant->id)->sum('montant');
            if ($resteAPayer > 0) {
                $alertes[] = [
                    'type' => 'danger',
                    'message' => "Solde impayé pour {$enfant->prenom} {$enfant->nom} : " . number_format($resteAPayer, 0, ',', ' ') . " FCFA",
                    'lien' => route('parent.paiements.index')
                ];
                break;
            }
        }
        
        return view('parent.dashboard', compact(
            'enfants',
            'stats',
            'moyennesParEnfant',
            'absencesParEnfant',
            'dernieresNotes',
            'evenementsRecents',
            'alertes',
            'anneeScolaire',
            'trimestreActuel'
        ));
    }
    
    private function getEvenementsRecents($enfants)
    {
        $evenements = collect();
        
        // Dernières notes
        $dernieresNotes = Note::whereIn('eleve_id', $enfants->pluck('id'))
            ->with(['matiere', 'eleve'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($note) {
                $color = $note->note >= 10 ? 'green' : 'orange';
                return (object)[
                    'type' => 'note',
                    'date' => $note->created_at,
                    'message' => "Note de {$note->note}/20 en {$note->matiere->nom} pour {$note->eleve->prenom}",
                    'icon' => 'fa-star',
                    'color' => $color,
                    'lien' => route('parent.notes.index'),
                ];
            });
        
        // Dernières absences
        $dernieresAbsences = Absence::whereIn('eleve_id', $enfants->pluck('id'))
            ->with(['eleve'])
            ->orderBy('date', 'desc')
            ->take(5)
            ->get()
            ->map(function($absence) {
                $color = $absence->justifiee ? 'blue' : 'red';
                return (object)[
                    'type' => 'absence',
                    'date' => $absence->date,
                    'message' => "Absence de {$absence->eleve->prenom} le {$absence->date->format('d/m/Y')}" . ($absence->justifiee ? ' (justifiée)' : ''),
                    'icon' => 'fa-calendar-times',
                    'color' => $color,
                    'lien' => route('parent.absences.index'),
                ];
            });
        
        // Derniers paiements
        $derniersPaiements = Paiement::whereIn('eleve_id', $enfants->pluck('id'))
            ->with(['eleve'])
            ->orderBy('date_paiement', 'desc')
            ->take(5)
            ->get()
            ->map(function($paiement) {
                return (object)[
                    'type' => 'paiement',
                    'date' => $paiement->date_paiement,
                    'message' => "Paiement de " . number_format($paiement->montant, 0, ',', ' ') . " FCFA pour {$paiement->eleve->prenom}",
                    'icon' => 'fa-money-bill-wave',
                    'color' => 'green',
                    'lien' => route('parent.paiements.index'),
                ];
            });
        
        return $dernieresNotes->merge($dernieresAbsences)->merge($derniersPaiements)
            ->sortByDesc('date')
            ->take(10);
    }
}