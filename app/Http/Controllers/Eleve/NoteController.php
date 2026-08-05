<?php
// app/Http/Controllers/Eleve/NoteController.php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\User;

class NoteController extends Controller
{
    /**
     * Affiche toutes les notes de l'élève
     */
    public function index()
    {
        $user = Auth::user();
        
        // Méthode 1: Chercher l'élève par user_id (si la colonne existe)
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        // Méthode 2: Par email (si user_id n'existe pas)
        if (!$eleve) {
            $eleve = Eleve::where('email', $user->email)->first();
        }
        
        // Méthode 3: Par email_parent
        if (!$eleve) {
            $eleve = Eleve::where('email_parent', $user->email)->first();
        }
        
        // Méthode 4: Par ID (si l'ID de l'utilisateur correspond à l'ID de l'élève)
        if (!$eleve) {
            $eleve = Eleve::find($user->id);
        }
        
        // Si toujours pas trouvé, afficher un message d'erreur
        if (!$eleve) {
            return redirect()->route('eleve.dashboard')->withErrors([
                'error' => 'Profil élève non trouvé. Veuillez contacter l\'administrateur pour lier votre compte à un élève.'
            ]);
        }
        
        // Récupérer les notes
        $notes = Note::where('eleve_id', $eleve->id)
            ->with(['matiere'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculer les moyennes par matière
        $moyennesParMatiere = [];
        foreach ($notes->groupBy('matiere_id') as $matiereId => $notesMatiere) {
            $matiere = $notesMatiere->first()->matiere;
            $moyennesParMatiere[$matiereId] = [
                'matiere' => $matiere->nom ?? 'Matière',
                'moyenne' => round($notesMatiere->avg('note'), 2),
                'notes' => $notesMatiere->count()
            ];
        }
        
        // Statistiques
        $stats = [
            'total_notes' => $notes->count(),
            'moyenne_generale' => round($notes->avg('note'), 2),
            'meilleure_note' => $notes->max('note') ?? 0,
            'plus_basse_note' => $notes->min('note') ?? 0,
        ];
        
        return view('eleve.notes', compact('notes', 'moyennesParMatiere', 'stats', 'eleve'));
    }
    
    /**
     * Affiche les détails d'une note
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $eleve = Eleve::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->orWhere('email_parent', $user->email)
            ->first();
        
        if (!$eleve) {
            return redirect()->route('eleve.dashboard')->withErrors(['error' => 'Profil élève non trouvé.']);
        }
        
        $note = Note::where('eleve_id', $eleve->id)
            ->with(['matiere', 'enseignant'])
            ->findOrFail($id);
        
        return view('eleve.note-show', compact('note'));
    }
}