<?php
// app/Http/Controllers/Parent/AbsenceController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Affiche la liste des absences des enfants
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $enfants = Eleve::where('email_parent', $user->email)
            ->orWhere('telephone_parent', $user->telephone)
            ->get();
        
        $enfantId = $request->get('enfant_id');
        $absencesParEnfant = [];
        $totalAbsences = 0;
        $totalRetards = 0;
        
        foreach ($enfants as $enfant) {
            if ($enfantId && $enfant->id != $enfantId) {
                continue;
            }
            
            $absences = Absence::where('eleve_id', $enfant->id)
                ->orderBy('date', 'desc')
                ->get();
            
            $totalAbsences += $absences->count();
            $totalRetards += $absences->where('est_retard', true)->count();
            
            $absencesParEnfant[] = [
                'enfant' => $enfant,
                'absences' => $absences,
                'total_absences' => $absences->count(),
                'total_retards' => $absences->where('est_retard', true)->count(),
            ];
        }
        
        $statistiques = [
            'total_absences' => $totalAbsences,
            'total_retards' => $totalRetards,
            'taux_presence' => 0, // À calculer selon le nombre de jours
        ];
        
        return view('parent.absences.index', compact('absencesParEnfant', 'enfants', 'statistiques'));
    }
    
    /**
     * Affiche les absences d'un enfant spécifique
     */
    public function enfant($id)
    {
        $user = Auth::user();
        
        $enfant = Eleve::where(function($q) use ($user) {
                $q->where('email_parent', $user->email)
                  ->orWhere('telephone_parent', $user->telephone);
            })
            ->findOrFail($id);
        
        $absences = Absence::where('eleve_id', $enfant->id)
            ->orderBy('date', 'desc')
            ->get();
        
        $stats = [
            'total_absences' => $absences->count(),
            'total_retards' => $absences->where('est_retard', true)->count(),
            'absences_non_justifiees' => $absences->where('justifiee', false)->count(),
        ];
        
        return view('parent.absences.enfant', compact('enfant', 'absences', 'stats'));
    }
    
    /**
     * Justifie une absence
     */
    public function justify(Request $request, $id)
    {
        $user = Auth::user();
        
        $absence = Absence::whereHas('eleve', function($q) use ($user) {
                $q->where('email_parent', $user->email)
                  ->orWhere('telephone_parent', $user->telephone);
            })
            ->findOrFail($id);
        
        $request->validate([
            'motif' => 'required|string|min:3|max:500',
        ]);
        
        $absence->update([
            'justifiee' => true,
            'motif' => $request->motif,
            'date_justification' => Carbon::now(),
        ]);
        
        return redirect()->back()->with('success', 'Absence justifiée avec succès.');
    }
}