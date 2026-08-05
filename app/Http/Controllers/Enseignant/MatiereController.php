<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\Note;
use App\Models\EmploiTemps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatiereController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        $matieres = $enseignant->matieres;
        
        $statistiques = [];
        foreach ($matieres as $matiere) {
            // Récupérer les classes où cette matière est enseignée
            $classes = EmploiTemps::where('enseignant_id', $enseignantId)
                ->where('matiere_id', $matiere->id)
                ->with('classe')
                ->get()
                ->pluck('classe')
                ->filter();
            
            // Compter le nombre total d'élèves
            $totalEleves = 0;
            foreach ($classes as $classe) {
                $totalEleves += $classe->eleves->count();
            }
            
            // Moyenne générale - CORRECTION : pas de relation 'evaluation'
            $moyenneGenerale = Note::where('enseignant_id', $enseignantId)
                ->where('matiere_id', $matiere->id)
                ->avg('note');
            
            // Meilleure note
            $meilleureNote = Note::where('enseignant_id', $enseignantId)
                ->where('matiere_id', $matiere->id)
                ->max('note');
            
            // Total notes
            $totalNotes = Note::where('enseignant_id', $enseignantId)
                ->where('matiere_id', $matiere->id)
                ->count();
            
            $statistiques[$matiere->id] = [
                'total_eleves' => $totalEleves,
                'moyenne_generale' => round($moyenneGenerale ?: 0, 2),
                'meilleure_note' => round($meilleureNote ?: 0, 2),
                'total_notes' => $totalNotes,
                'classes' => $classes,
            ];
        }
        
        return view('enseignant.matieres.index', compact('matieres', 'statistiques'));
    }
    
    public function show($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Vérifier que l'enseignant enseigne cette matière
        $matiere = $enseignant->matieres()->where('matieres.id', $id)->first();
        
        if (!$matiere) {
            return redirect()->route('enseignant.matieres.index')
                ->with('error', 'Vous n\'enseignez pas cette matière.');
        }
        
        // Récupérer les classes où cette matière est enseignée
        $classes = EmploiTemps::where('enseignant_id', $enseignantId)
            ->where('matiere_id', $id)
            ->with(['classe.eleves'])
            ->get()
            ->pluck('classe')
            ->filter()
            ->unique('id');
        
        // Dernières notes saisies pour cette matière
        $dernieresNotes = Note::where('enseignant_id', $enseignantId)
            ->where('matiere_id', $id)
            ->with(['eleve', 'classe'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Statistiques de la matière
        $totalNotes = Note::where('enseignant_id', $enseignantId)
            ->where('matiere_id', $id)
            ->count();
        
        $moyenneGenerale = Note::where('enseignant_id', $enseignantId)
            ->where('matiere_id', $id)
            ->avg('note');
        
        $meilleureNote = Note::where('enseignant_id', $enseignantId)
            ->where('matiere_id', $id)
            ->max('note');
        
        $plusFaibleNote = Note::where('enseignant_id', $enseignantId)
            ->where('matiere_id', $id)
            ->min('note');
        
        // Distribution des notes
        $notes = Note::where('enseignant_id', $enseignantId)
            ->where('matiere_id', $id)
            ->get();
        
        $distribution = [
            'excellent' => $notes->where('note', '>=', 16)->count(),
            'tres_bien' => $notes->whereBetween('note', [14, 15.99])->count(),
            'bien' => $notes->whereBetween('note', [12, 13.99])->count(),
            'passable' => $notes->whereBetween('note', [10, 11.99])->count(),
            'insuffisant' => $notes->whereBetween('note', [8, 9.99])->count(),
            'faible' => $notes->where('note', '<', 8)->count(),
        ];
        
        $stats = [
            'total_notes' => $totalNotes,
            'moyenne_generale' => round($moyenneGenerale ?: 0, 2),
            'meilleure_note' => round($meilleureNote ?: 0, 2),
            'plus_faible_note' => round($plusFaibleNote ?: 0, 2),
            'distribution' => $distribution,
        ];
        
        return view('enseignant.matieres.show', compact('matiere', 'classes', 'dernieresNotes', 'stats'));
    }
}