<?php
// app/Http/Controllers/Enseignant/EmploiTempsController.php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\EmploiTemps;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\Horaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmploiTempsController extends Controller
{
    /**
     * Récupère les plages horaires
     */
    private function getPlagesHoraires()
    {
        // Récupérer depuis la table horaires ou utiliser les valeurs par défaut
        $plages = [
            '07:30' => '07:30 - 08:00',
            '08:00' => '08:00 - 08:30',
            '08:30' => '08:30 - 09:00',
            '09:00' => '09:00 - 09:30',
            '09:30' => '09:30 - 10:00',
            '10:00' => '10:00 - 10:30',
            '10:30' => '10:30 - 11:00',
            '11:00' => '11:00 - 11:30',
            '11:30' => '11:30 - 12:00',
            '12:00' => '12:00 - 12:30',
            'Pause déjeuner' => '12:30 - 13:30',
            '13:30' => '13:30 - 14:00',
            '14:00' => '14:00 - 14:30',
            '14:30' => '14:30 - 15:00',
            '15:00' => '15:00 - 15:30',
            '15:30' => '15:30 - 16:00',
            '16:00' => '16:00 - 16:30',
            '16:30' => '16:30 - 17:00',
            '17:00' => '17:00 - 17:30',
            '17:30' => '17:30 - 18:00',
        ];
        
        return $plages;
    }

    /**
     * Affiche l'emploi du temps de l'enseignant
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Récupérer la semaine à afficher
        $semaine = $request->get('semaine', Carbon::now()->weekOfYear);
        $annee = $request->get('annee', Carbon::now()->year);
        
        // Dates de début et fin de semaine
        $dateDebut = Carbon::now()->setISODate($annee, $semaine)->startOfWeek();
        $dateFin = Carbon::now()->setISODate($annee, $semaine)->endOfWeek();
        
        // Récupérer l'emploi du temps
        $emploisTemps = EmploiTemps::where('enseignant_id', $enseignantId)
            ->with(['classe', 'matiere'])
            ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
            ->orderBy('heure_debut')
            ->get();
        
        // Organisation par jour
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $emploiParJour = [];
        
        foreach ($jours as $jour) {
            $emploiParJour[$jour] = $emploisTemps->where('jour', $jour)->values();
        }
        
        // Récupérer les classes attribuées à l'enseignant
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->get();
        
        $classesPrincipales = $enseignant->classes;
        $classes = $classes->merge($classesPrincipales)->unique('id');
        
        $matieres = $enseignant->matieres;
        
        // Filtre par classe
        $classeId = $request->get('classe_id');
        if ($classeId) {
            $emploisTemps = $emploisTemps->where('classe_id', $classeId);
            foreach ($jours as $jour) {
                $emploiParJour[$jour] = $emploisTemps->where('jour', $jour)->values();
            }
        }
        
        // Plages horaires dynamiques
        $plagesHoraires = $this->getPlagesHoraires();
        
        // Statistiques hebdomadaires
        $statsHebdo = [
            'total_heures' => $emploisTemps->sum(function($cours) {
                $debut = Carbon::parse($cours->heure_debut);
                $fin = Carbon::parse($cours->heure_fin);
                return $debut->diffInHours($fin);
            }),
            'total_cours' => $emploisTemps->count(),
            'classes_enseignees' => $emploisTemps->pluck('classe.nom')->unique()->count(),
            'matieres_enseignees' => $emploisTemps->pluck('matiere.nom')->unique()->count(),
        ];
        
        return view('enseignant.emploi_temps.index', compact(
            'emploiParJour',
            'jours',
            'classes',
            'matieres',
            'classeId',
            'semaine',
            'annee',
            'dateDebut',
            'dateFin',
            'statsHebdo',
            'enseignantId',
            'plagesHoraires'
        ));
    }
    
    /**
     * Affiche le formulaire d'ajout d'un cours
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Classes attribuées à l'enseignant
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->get();
        
        $classesPrincipales = $enseignant->classes;
        $classes = $classes->merge($classesPrincipales)->unique('id');
        
        $matieres = $enseignant->matieres;
        
        $jour = $request->get('jour', 'Lundi');
        $heure = $request->get('heure', '08:00');
        $plagesHoraires = $this->getPlagesHoraires();
        
        // Calcul de l'heure fin par défaut
        $heures = array_keys($plagesHoraires);
        $indexHeure = array_search($heure, $heures);
        $heureFin = isset($heures[$indexHeure + 1]) ? $heures[$indexHeure + 1] : '09:00';
        
        return view('enseignant.emploi_temps.create', compact('classes', 'matieres', 'jour', 'heure', 'heureFin', 'plagesHoraires'));
    }
    
    /**
     * Enregistre un nouveau cours dans l'emploi du temps
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'heure_debut' => 'required',
            'heure_fin' => 'required',
            'salle' => 'nullable|string|max:100',
        ]);
        
        // Vérifier que l'enseignant a bien accès à cette classe
        $aAccesClasse = Classe::where('id', $request->classe_id)
            ->where(function($q) use ($enseignant) {
                $q->whereHas('emploisTemps', function($sub) use ($enseignant) {
                    $sub->where('enseignant_id', $enseignant->user_id);
                })->orWhere('professeur_principal_id', $enseignant->user_id);
            })
            ->exists();
        
        if (!$aAccesClasse) {
            return redirect()->back()->with('error', 'Vous n\'avez pas accès à cette classe.')->withInput();
        }
        
        // Vérifier qu'il n'y a pas de conflit d'horaire
        $conflit = EmploiTemps::where('enseignant_id', $enseignant->user_id)
            ->where('jour', $request->jour)
            ->where(function($q) use ($request) {
                $q->whereBetween('heure_debut', [$request->heure_debut, $request->heure_fin])
                  ->orWhereBetween('heure_fin', [$request->heure_debut, $request->heure_fin])
                  ->orWhere(function($sub) use ($request) {
                      $sub->where('heure_debut', '<=', $request->heure_debut)
                          ->where('heure_fin', '>=', $request->heure_fin);
                  });
            })
            ->exists();
        
        if ($conflit) {
            return redirect()->back()->with('error', 'Un cours existe déjà sur ce créneau horaire.')->withInput();
        }
        
        EmploiTemps::create([
            'enseignant_id' => $enseignant->user_id,
            'classe_id' => $request->classe_id,
            'matiere_id' => $request->matiere_id,
            'jour' => $request->jour,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'salle' => $request->salle,
        ]);
        
        return redirect()->route('enseignant.emploi_temps.index')
            ->with('success', 'Cours ajouté avec succès.');
    }
    
    /**
     * Affiche le formulaire d'édition d'un cours
     */
    public function edit($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        $cours = EmploiTemps::where('enseignant_id', $enseignant->user_id)
            ->with(['classe', 'matiere'])
            ->findOrFail($id);
        
        // Classes attribuées à l'enseignant
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->get();
        
        $classesPrincipales = $enseignant->classes;
        $classes = $classes->merge($classesPrincipales)->unique('id');
        
        $matieres = $enseignant->matieres;
        $plagesHoraires = $this->getPlagesHoraires();
        
        return view('enseignant.emploi_temps.edit', compact('cours', 'classes', 'matieres', 'plagesHoraires'));
    }
    
    /**
     * Met à jour un cours
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $cours = EmploiTemps::where('enseignant_id', $enseignant->user_id)->findOrFail($id);
        
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'jour' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'heure_debut' => 'required',
            'heure_fin' => 'required',
            'salle' => 'nullable|string|max:100',
        ]);
        
        // Vérifier que l'enseignant a bien accès à cette classe
        $aAccesClasse = Classe::where('id', $request->classe_id)
            ->where(function($q) use ($enseignant) {
                $q->whereHas('emploisTemps', function($sub) use ($enseignant) {
                    $sub->where('enseignant_id', $enseignant->user_id);
                })->orWhere('professeur_principal_id', $enseignant->user_id);
            })
            ->exists();
        
        if (!$aAccesClasse) {
            return redirect()->back()->with('error', 'Vous n\'avez pas accès à cette classe.')->withInput();
        }
        
        // Vérifier les conflits (exclure le cours actuel)
        $conflit = EmploiTemps::where('enseignant_id', $enseignant->user_id)
            ->where('id', '!=', $id)
            ->where('jour', $request->jour)
            ->where(function($q) use ($request) {
                $q->whereBetween('heure_debut', [$request->heure_debut, $request->heure_fin])
                  ->orWhereBetween('heure_fin', [$request->heure_debut, $request->heure_fin])
                  ->orWhere(function($sub) use ($request) {
                      $sub->where('heure_debut', '<=', $request->heure_debut)
                          ->where('heure_fin', '>=', $request->heure_fin);
                  });
            })
            ->exists();
        
        if ($conflit) {
            return redirect()->back()->with('error', 'Un cours existe déjà sur ce créneau horaire.')->withInput();
        }
        
        $cours->update([
            'classe_id' => $request->classe_id,
            'matiere_id' => $request->matiere_id,
            'jour' => $request->jour,
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'salle' => $request->salle,
        ]);
        
        return redirect()->route('enseignant.emploi_temps.index')
            ->with('success', 'Cours modifié avec succès.');
    }
    
    /**
     * Supprime un cours
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $cours = EmploiTemps::where('enseignant_id', $enseignant->user_id)->findOrFail($id);
        $cours->delete();
        
        return redirect()->route('enseignant.emploi_temps.index')
            ->with('success', 'Cours supprimé avec succès.');
    }
    
    /**
     * Affiche l'emploi du temps pour une semaine spécifique
     */
    public function semaine(Request $request)
    {
        $semaine = $request->get('semaine', Carbon::now()->weekOfYear);
        $annee = $request->get('annee', Carbon::now()->year);
        
        return redirect()->route('enseignant.emploi_temps.index', [
            'semaine' => $semaine,
            'annee' => $annee,
            'classe_id' => $request->get('classe_id')
        ]);
    }
    
    /**
     * Affiche l'emploi du temps pour un jour spécifique
     */
    public function jour(Request $request, $date = null)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        $dateCourante = $date ? Carbon::parse($date) : Carbon::now();
        $jourSemaine = $dateCourante->locale('fr')->dayName;
        
        $coursDuJour = EmploiTemps::where('enseignant_id', $enseignantId)
            ->where('jour', $jourSemaine)
            ->with(['classe', 'matiere'])
            ->orderBy('heure_debut')
            ->get();
        
        $plagesHoraires = $this->getPlagesHoraires();
        
        return view('enseignant.emploi_temps.jour', compact('dateCourante', 'jourSemaine', 'coursDuJour', 'plagesHoraires'));
    }
}