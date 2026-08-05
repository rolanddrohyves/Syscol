<?php
// app/Http/Controllers/Eleve/CoursController.php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cours;
use App\Models\Eleve;

class CoursController extends Controller
{
    /**
     * Affiche la liste des cours
     */
    public function index()
    {
        $user = Auth::user();
        
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        if (!$eleve) {
            return redirect()->route('eleve.dashboard')->with('error', 'Profil élève non trouvé.');
        }
        
        // Récupérer les cours par matière
        $coursParMatiere = Cours::where('classe_id', $eleve->classe_id)
            ->with(['matiere', 'enseignant'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('matiere_id');
        
        return view('eleve.cours', compact('coursParMatiere', 'eleve'));
    }
    
    /**
     * Affiche les détails d'un cours
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        $cours = Cours::where('classe_id', $eleve->classe_id)
            ->with(['matiere', 'enseignant', 'documents'])
            ->findOrFail($id);
        
        return view('eleve.cours-show', compact('cours'));
    }
}