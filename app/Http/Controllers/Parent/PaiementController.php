<?php
// app/Http/Controllers/Parent/PaiementController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaiementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 🔒 Récupérer UNIQUEMENT les enfants du parent connecté
        $enfants = Eleve::where('parent_id', $user->id)
            ->with('classe')
            ->get();
        
        $paiementsParEnfant = [];
        $totalGlobalPaye = 0;
        $totalGlobalAttendu = 0;
        
        foreach ($enfants as $enfant) {
            // 🔒 Récupérer UNIQUEMENT les paiements de CET enfant
            $paiements = Paiement::where('eleve_id', $enfant->id)
                ->orderBy('date_paiement', 'desc')
                ->get();
            
            $totalPaye = $paiements->sum('montant');
            $totalFrais = $enfant->montant_total_frais ?? 0;
            $resteAPayer = $totalFrais - $totalPaye;
            
            $totalGlobalPaye += $totalPaye;
            $totalGlobalAttendu += $totalFrais;
            
            $paiementsParEnfant[] = [
                'enfant' => $enfant,
                'paiements' => $paiements,
                'total_paye' => $totalPaye,
                'total_frais' => $totalFrais,
                'reste_a_payer' => $resteAPayer > 0 ? $resteAPayer : 0,
                'taux_paiement' => $totalFrais > 0 ? round(($totalPaye / $totalFrais) * 100, 2) : 0,
            ];
        }
        
        return view('parent.paiements.index', compact('paiementsParEnfant', 'totalGlobalPaye', 'totalGlobalAttendu'));
    }
    
    public function enfant($id)
    {
        $user = Auth::user();
        
        // 🔒 Vérification stricte : l'enfant doit appartenir au parent
        $enfant = Eleve::where('id', $id)
            ->where('parent_id', $user->id)
            ->with('classe')
            ->firstOrFail();
        
        $paiements = Paiement::where('eleve_id', $enfant->id)
            ->orderBy('date_paiement', 'desc')
            ->get();
        
        $totalPaye = $paiements->sum('montant');
        $totalFrais = $enfant->montant_total_frais ?? 0;
        $resteAPayer = $totalFrais - $totalPaye;
        
        return view('parent.paiements.enfant', compact('enfant', 'paiements', 'totalPaye', 'totalFrais', 'resteAPayer'));
    }
    
    public function recu($id)
    {
        $user = Auth::user();
        
        // 🔒 Vérification stricte : le paiement doit appartenir à un enfant du parent
        $paiement = Paiement::where('id', $id)
            ->whereHas('eleve', function($query) use ($user) {
                $query->where('parent_id', $user->id);
            })
            ->with(['eleve.classe', 'frais'])
            ->firstOrFail();
        
        return view('parent.paiements.recu', compact('paiement'));
    }
}