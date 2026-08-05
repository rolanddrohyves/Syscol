<?php
// app/Http/Controllers/Parent/EnfantController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Note;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnfantController extends Controller
{
    /**
     * Affiche la liste des enfants du parent
     */
    public function index()
    {
        $user = Auth::user();
        
        $enfants = Eleve::where('email_parent', $user->email)
            ->orWhere('telephone_parent', $user->telephone)
            ->with(['classe', 'notes', 'absences'])
            ->get();
        
        return view('parent.enfants.index', compact('enfants'));
    }
    
    /**
     * Affiche les détails d'un enfant
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $enfant = Eleve::where(function($q) use ($user) {
                $q->where('email_parent', $user->email)
                  ->orWhere('telephone_parent', $user->telephone);
            })
            ->with(['classe', 'notes.matiere', 'absences'])
            ->findOrFail($id);
        
        return view('parent.enfants.show', compact('enfant'));
    }
}