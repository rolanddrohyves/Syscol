<?php
// app/Http/Controllers/Eleve/DevoirController.php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Devoir;
use App\Models\Eleve;

class DevoirController extends Controller
{
    /**
     * Affiche la liste des devoirs
     */
    public function index()
    {
        $user = Auth::user();
        
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        if (!$eleve) {
            return redirect()->route('eleve.dashboard')->with('error', 'Profil élève non trouvé.');
        }
        
        // Devoirs à venir
        $devoirsAVenir = Devoir::where('classe_id', $eleve->classe_id)
            ->whereDate('date_limite', '>=', now())
            ->with(['matiere'])
            ->orderBy('date_limite', 'asc')
            ->get();
        
        // Devoirs passés
        $devoirsPasses = Devoir::where('classe_id', $eleve->classe_id)
            ->whereDate('date_limite', '<', now())
            ->with(['matiere'])
            ->orderBy('date_limite', 'desc')
            ->take(10)
            ->get();
        
        // Statistiques
        $stats = [
            'total_a_venir' => $devoirsAVenir->count(),
            'total_passés' => $devoirsPasses->count(),
            'prochain_devoir' => $devoirsAVenir->first(),
        ];
        
        return view('eleve.devoirs', compact('devoirsAVenir', 'devoirsPasses', 'stats', 'eleve'));
    }
    
    /**
     * Affiche les détails d'un devoir
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        $devoir = Devoir::where('classe_id', $eleve->classe_id)
            ->with(['matiere', 'enseignant'])
            ->findOrFail($id);
        
        return view('eleve.devoir-show', compact('devoir'));
    }
}