<?php
// app/Http/Controllers/Parent/BulletinController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Note;
use App\Models\Trimestre;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BulletinController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer les enfants du parent
        $enfants = Eleve::where('email_parent', $user->email)
            ->orWhere('telephone_parent', $user->telephone)
            ->with(['classe'])
            ->get();
        
        $enfantId = $request->get('enfant_id');
        $bulletins = [];
        
        // Récupérer l'année scolaire en cours
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->where('is_current', true)
            ->first();
        
        // Récupérer les trimestres
        $trimestres = Trimestre::where('annee_scolaire_id', $anneeScolaire?->id)
            ->orderBy('numero')
            ->get();
        
        foreach ($enfants as $enfant) {
            if ($enfantId && $enfant->id != $enfantId) {
                continue;
            }
            
            $bulletinsEnfant = [];
            $matieres = [];
            
            // Récupérer les matières de la classe
            if ($enfant->classe) {
                $classeMatieres = $enfant->classe->matieres;
                foreach ($classeMatieres as $matiere) {
                    $matieres[$matiere->id] = [
                        'nom' => $matiere->nom,
                        't1' => 0,
                        't2' => 0,
                        't3' => 0,
                        'moyenne' => 0,
                    ];
                }
            }
            
            // Récupérer les notes par trimestre
            foreach ($trimestres as $trimestre) {
                $notes = Note::where('eleve_id', $enfant->id)
                    ->where('trimestre_id', $trimestre->id)
                    ->with(['matiere'])
                    ->get();
                
                $moyenne = $notes->avg('note') ?: 0;
                
                // Remplir les notes par matière
                foreach ($notes as $note) {
                    if (isset($matieres[$note->matiere_id])) {
                        $trimestreKey = 't' . $trimestre->numero;
                        $matieres[$note->matiere_id][$trimestreKey] = round($note->note, 2);
                    }
                }
                
                $bulletinsEnfant[] = [
                    'id' => $trimestre->id,
                    'trimestre' => $trimestre,
                    'moyenne' => round($moyenne, 2),
                    'rang' => $this->calculerRang($enfant->id, $trimestre->id),
                    'total_eleves' => $this->getTotalElevesClasse($enfant->classe_id),
                    'appreciation' => $this->getAppreciation($moyenne),
                ];
            }
            
            // Calculer les moyennes par matière
            foreach ($matieres as $id => $matiere) {
                $notesMatiere = [];
                if ($matiere['t1'] > 0) $notesMatiere[] = $matiere['t1'];
                if ($matiere['t2'] > 0) $notesMatiere[] = $matiere['t2'];
                if ($matiere['t3'] > 0) $notesMatiere[] = $matiere['t3'];
                $matieres[$id]['moyenne'] = !empty($notesMatiere) ? round(array_sum($notesMatiere) / count($notesMatiere), 2) : 0;
            }
            
            $bulletins[] = [
                'enfant' => $enfant,
                'bulletins' => $bulletinsEnfant,
                'matieres' => $matieres,
                'moyenne_generale' => round(collect($bulletinsEnfant)->avg('moyenne'), 2),
            ];
        }
        
        return view('parent.bulletins.index', compact('bulletins', 'enfants'));
    }
    
    public function enfant($id)
    {
        $user = Auth::user();
        
        $enfant = Eleve::where(function($q) use ($user) {
                $q->where('email_parent', $user->email)
                  ->orWhere('telephone_parent', $user->telephone);
            })
            ->with(['classe.matieres'])
            ->findOrFail($id);
        
        // Récupérer l'année scolaire en cours
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->where('is_current', true)
            ->first();
        
        $trimestres = Trimestre::where('annee_scolaire_id', $anneeScolaire?->id)
            ->orderBy('numero')
            ->get();
        
        $bulletins = [];
        foreach ($trimestres as $trimestre) {
            $notes = Note::where('eleve_id', $enfant->id)
                ->where('trimestre_id', $trimestre->id)
                ->with(['matiere'])
                ->get();
            
            $moyenne = $notes->avg('note') ?: 0;
            
            $bulletins[] = [
                'trimestre' => $trimestre,
                'notes' => $notes,
                'moyenne' => round($moyenne, 2),
                'rang' => $this->calculerRang($enfant->id, $trimestre->id),
                'total_eleves' => $this->getTotalElevesClasse($enfant->classe_id),
            ];
        }
        
        return view('parent.bulletins.enfant', compact('enfant', 'bulletins'));
    }
    
    public function pdf($id)
    {
        $user = Auth::user();
        
        $trimestre = Trimestre::findOrFail($id);
        
        // Récupérer l'enfant via les notes du trimestre
        $enfant = Eleve::whereHas('notes', function($q) use ($id, $user) {
                $q->where('trimestre_id', $id);
                $q->whereHas('eleve', function($sub) use ($user) {
                    $sub->where('email_parent', $user->email)
                        ->orWhere('telephone_parent', $user->telephone);
                });
            })
            ->with(['classe.matieres'])
            ->firstOrFail();
        
        $notes = Note::where('eleve_id', $enfant->id)
            ->where('trimestre_id', $id)
            ->with(['matiere'])
            ->get();
        
        $moyenne = $notes->avg('note') ?: 0;
        $rang = $this->calculerRang($enfant->id, $id);
        $totalEleves = $this->getTotalElevesClasse($enfant->classe_id);
        
        $data = [
            'enfant' => $enfant,
            'trimestre' => $trimestre,
            'notes' => $notes,
            'moyenne' => round($moyenne, 2),
            'rang' => $rang,
            'total_eleves' => $totalEleves,
            'appreciation' => $this->getAppreciation($moyenne),
            'date_edition' => now(),
        ];
        
        $pdf = PDF::loadView('parent.bulletins.pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('bulletin_' . $enfant->nom . '_' . $trimestre->libelle . '.pdf');
    }
    
    private function calculerRang($eleveId, $trimestreId)
    {
        $eleve = Eleve::find($eleveId);
        if (!$eleve || !$eleve->classe_id) {
            return 'N/A';
        }
        
        $elevesClasse = Eleve::where('classe_id', $eleve->classe_id)->pluck('id');
        
        $moyennes = [];
        foreach ($elevesClasse as $id) {
            $moyenne = Note::where('eleve_id', $id)
                ->where('trimestre_id', $trimestreId)
                ->avg('note');
            $moyennes[$id] = $moyenne ?: 0;
        }
        
        arsort($moyennes);
        
        $rang = 1;
        foreach ($moyennes as $id => $moyenne) {
            if ($id == $eleveId) {
                return $rang;
            }
            $rang++;
        }
        
        return 'N/A';
    }
    
    private function getTotalElevesClasse($classeId)
    {
        return Eleve::where('classe_id', $classeId)->count();
    }
    
    private function getAppreciation($moyenne)
    {
        if ($moyenne >= 16) {
            return 'Excellent travail ! Félicitations pour cette excellente performance. Continuez sur cette lancée.';
        } elseif ($moyenne >= 14) {
            return 'Très bien ! De très bons résultats. Continuez vos efforts.';
        } elseif ($moyenne >= 12) {
            return 'Bien ! Des résultats satisfaisants. Quelques efforts supplémentaires vous permettront de progresser encore.';
        } elseif ($moyenne >= 10) {
            return 'Passable. Des efforts sont nécessaires pour améliorer vos résultats.';
        } elseif ($moyenne >= 8) {
            return 'Insuffisant. Un travail plus régulier est recommandé.';
        } else {
            return 'Des progrès significatifs sont à faire. Une aide supplémentaire pourrait être bénéfique.';
        }
    }
}