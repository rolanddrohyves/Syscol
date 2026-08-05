<?php
// app/Http/Controllers/Parent/NoteController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NoteController extends Controller
{
    /**
     * Affiche la liste des notes des enfants
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer les enfants du parent
        $enfants = Eleve::where('email_parent', $user->email)
            ->orWhere('telephone_parent', $user->telephone)
            ->with(['classe'])
            ->get();
        
        $enfantId = $request->get('enfant_id');
        $notesParEnfant = [];
        
        foreach ($enfants as $enfant) {
            if ($enfantId && $enfant->id != $enfantId) {
                continue;
            }
            
            $notes = Note::where('eleve_id', $enfant->id)
                ->with(['matiere'])
                ->orderBy('date_evaluation', 'desc')
                ->get();
            
            // Calculer les moyennes par matière pour la classe
            $moyennesClasse = [];
            if ($enfant->classe) {
                $elevesClasse = Eleve::where('classe_id', $enfant->classe->id)->pluck('id');
                $matieres = $enfant->classe->matieres ?? [];
                
                foreach ($matieres as $matiere) {
                    $moyenne = Note::whereIn('eleve_id', $elevesClasse)
                        ->where('matiere_id', $matiere->id)
                        ->avg('note');
                    $moyennesClasse[$matiere->id] = round($moyenne ?: 0, 2);
                }
            }
            
            $moyenneGenerale = $notes->avg('note') ?: 0;
            
            $notesParEnfant[] = [
                'enfant' => $enfant,
                'notes' => $notes,
                'moyenne_generale' => round($moyenneGenerale, 2),
                'moyennes_classe' => $moyennesClasse,
            ];
        }
        
        // Si un enfant est sélectionné, ne garder que lui
        if ($enfantId) {
            $notesParEnfant = array_filter($notesParEnfant, function($item) use ($enfantId) {
                return $item['enfant']->id == $enfantId;
            });
        }
        
        return view('parent.notes.index', compact('notesParEnfant', 'enfants'));
    }
    
    /**
     * Affiche les notes d'un enfant spécifique
     */
    public function enfant($id)
    {
        $user = Auth::user();
        
        $enfant = Eleve::where(function($q) use ($user) {
                $q->where('email_parent', $user->email)
                  ->orWhere('telephone_parent', $user->telephone);
            })
            ->with(['classe.matieres'])
            ->findOrFail($id);
        
        $notes = Note::where('eleve_id', $enfant->id)
            ->with(['matiere'])
            ->orderBy('date_evaluation', 'desc')
            ->get();
        
        // Moyennes par trimestre
        $moyennesParTrimestre = [
            1 => $notes->where('trimestre_id', 1)->avg('note') ?: 0,
            2 => $notes->where('trimestre_id', 2)->avg('note') ?: 0,
            3 => $notes->where('trimestre_id', 3)->avg('note') ?: 0,
        ];
        
        $moyenneGenerale = $notes->avg('note') ?: 0;
        
        return view('parent.notes.enfant', compact('enfant', 'notes', 'moyenneGenerale', 'moyennesParTrimestre'));
    }
    
    /**
     * Affiche le détail d'une note
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $note = Note::with(['eleve', 'matiere'])
            ->whereHas('eleve', function($q) use ($user) {
                $q->where('email_parent', $user->email)
                  ->orWhere('telephone_parent', $user->telephone);
            })
            ->firstOrFail();
        
        return view('parent.notes.show', compact('note'));
    }
}