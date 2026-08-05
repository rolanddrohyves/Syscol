<?php
// app/Http/Controllers/Parent/EmploiTempsController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\EmploiTemps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmploiTempsController extends Controller
{
    /**
     * Affiche l'emploi du temps des enfants
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $enfants = Eleve::where('email_parent', $user->email)
            ->orWhere('telephone_parent', $user->telephone)
            ->with(['classe'])
            ->get();
        
        $enfantId = $request->get('enfant_id');
        $semaine = $request->get('semaine', Carbon::now()->weekOfYear);
        $annee = $request->get('annee', Carbon::now()->year);
        
        $dateDebut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
        $dateFin = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();
        
        $emploisParEnfant = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        
        foreach ($enfants as $enfant) {
            if ($enfantId && $enfant->id != $enfantId) {
                continue;
            }
            
            if (!$enfant->classe) {
                continue;
            }
            
            $emploisTemps = EmploiTemps::where('classe_id', $enfant->classe->id)
                ->with(['matiere'])
                ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
                ->orderBy('heure_debut')
                ->get();
            
            $emploiParJour = [];
            foreach ($jours as $jour) {
                $emploiParJour[$jour] = $emploisTemps->where('jour', $jour)->values();
            }
            
            $emploisParEnfant[] = [
                'enfant' => $enfant,
                'emploiParJour' => $emploiParJour,
            ];
        }
        
        $plagesHoraires = [
            '08:00' => '08h00 - 10h00',
            '10:00' => '10h00 - 12h00',
            '14:00' => '14h00 - 16h00',
            '16:00' => '16h00 - 18h00',
        ];
        
        return view('parent.emploi_temps.index', compact(
            'emploisParEnfant', 
            'enfants', 
            'jours', 
            'plagesHoraires',
            'semaine',
            'annee',
            'dateDebut',
            'dateFin'
        ));
    }
    
    /**
     * Affiche l'emploi du temps d'un enfant spécifique
     */
    public function enfant($id, Request $request)
    {
        $user = Auth::user();
        
        $enfant = Eleve::where(function($q) use ($user) {
                $q->where('email_parent', $user->email)
                  ->orWhere('telephone_parent', $user->telephone);
            })
            ->with(['classe'])
            ->findOrFail($id);
        
        if (!$enfant->classe) {
            return redirect()->back()->with('error', 'Cet enfant n\'a pas de classe assignée.');
        }
        
        $semaine = $request->get('semaine', Carbon::now()->weekOfYear);
        $annee = $request->get('annee', Carbon::now()->year);
        
        $dateDebut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
        $dateFin = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();
        
        $emploisTemps = EmploiTemps::where('classe_id', $enfant->classe->id)
            ->with(['matiere'])
            ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
            ->orderBy('heure_debut')
            ->get();
        
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $emploiParJour = [];
        
        foreach ($jours as $jour) {
            $emploiParJour[$jour] = $emploisTemps->where('jour', $jour)->values();
        }
        
        $plagesHoraires = [
            '08:00' => '08h00 - 10h00',
            '10:00' => '10h00 - 12h00',
            '14:00' => '14h00 - 16h00',
            '16:00' => '16h00 - 18h00',
        ];
        
        return view('parent.emploi_temps.enfant', compact(
            'enfant', 
            'emploiParJour', 
            'jours', 
            'plagesHoraires',
            'semaine',
            'annee',
            'dateDebut',
            'dateFin'
        ));
    }
    
    /**
     * Affiche l'emploi du temps pour une semaine spécifique
     */
    public function semaine(Request $request, $id)
    {
        $semaine = $request->get('semaine', Carbon::now()->weekOfYear);
        $annee = $request->get('annee', Carbon::now()->year);
        
        return redirect()->route('parent.emploi_temps.enfant', [
            'enfant' => $id,
            'semaine' => $semaine,
            'annee' => $annee
        ]);
    }
}