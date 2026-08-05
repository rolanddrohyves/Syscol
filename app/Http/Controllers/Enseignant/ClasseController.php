<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\EmploiTemps;
use App\Models\Eleve;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClasseController extends Controller
{
    /**
     * Affiche la liste des classes de l'enseignant
     */
    public function index()
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Récupérer les classes où l'enseignant enseigne
        $classesEnseignees = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->with(['matieres' => function($q) use ($enseignantId) {
            $q->whereHas('emploisTemps', function($sub) use ($enseignantId) {
                $sub->where('enseignant_id', $enseignantId);
            });
        }])->get();
        
        // Récupérer les classes où l'enseignant est professeur principal
        $classesPrincipales = $enseignant->classes()->with('matieres')->get();
        
        // Fusionner les deux collections sans doublons
        $classes = $classesEnseignees->merge($classesPrincipales)->unique('id');
        
        // Statistiques par classe
        $statistiques = [];
        foreach ($classes as $classe) {
            $totalEleves = $classe->eleves->count();
            $totalMatieres = $classe->matieres->count();
            $totalCoursHebdo = EmploiTemps::where('classe_id', $classe->id)
                ->where('enseignant_id', $enseignantId)
                ->count();
            
            // Nombre d'absences ce mois
            $absencesMois = \App\Models\Absence::whereHas('eleve', function($q) use ($classe) {
                $q->where('classe_id', $classe->id);
            })->whereMonth('date', now()->month)->count();
            
            // Nombre de notes saisies
            $notesSaisies = \App\Models\Note::where('enseignant_id', $enseignantId)
                ->whereHas('eleve', function($q) use ($classe) {
                    $q->where('classe_id', $classe->id);
                })
                ->count();
            
            $statistiques[$classe->id] = [
                'total_eleves' => $totalEleves,
                'total_matieres' => $totalMatieres,
                'total_cours_hebdo' => $totalCoursHebdo,
                'absences_mois' => $absencesMois,
                'notes_saisies' => $notesSaisies,
                'taux_saisie' => $totalEleves > 0 ? round(($notesSaisies / ($totalEleves * 5)) * 100, 2) : 0,
            ];
        }
        
        return view('enseignant.classes.index', compact('classes', 'statistiques'));
    }
    
    /**
     * Affiche les détails d'une classe
     */
    public function show($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Vérifier que l'enseignant a accès à cette classe
        $aAcces = Classe::where('id', $id)
            ->where(function($q) use ($enseignantId) {
                $q->whereHas('emploisTemps', function($sub) use ($enseignantId) {
                    $sub->where('enseignant_id', $enseignantId);
                })->orWhere('professeur_principal_id', $enseignantId);
            })
            ->exists();
        
        if (!$aAcces) {
            return redirect()->route('enseignant.classes')
                ->with('error', 'Vous n\'avez pas accès à cette classe.');
        }
        
        $classe = Classe::with(['eleves' => function($q) {
                $q->orderBy('nom')->orderBy('prenom');
            }, 'matieres' => function($q) use ($enseignantId) {
                $q->whereHas('emploisTemps', function($sub) use ($enseignantId) {
                    $sub->where('enseignant_id', $enseignantId);
                });
            }])->findOrFail($id);
        
        // Emploi du temps de la classe
        $emploiTemps = EmploiTemps::where('classe_id', $id)
            ->where('enseignant_id', $enseignantId)
            ->with('matiere')
            ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi')")
            ->orderBy('heure_debut')
            ->get();
        
        // Groupement par jour
        $emploiParJour = [];
        foreach ($emploiTemps as $cours) {
            $emploiParJour[$cours->jour][] = $cours;
        }
        
        // Dernières notes saisies pour cette classe
        $dernieresNotes = \App\Models\Note::where('enseignant_id', $enseignantId)
            ->whereHas('eleve', function($q) use ($id) {
                $q->where('classe_id', $id);
            })
            ->with(['eleve', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Statistiques de la classe
        $totalEleves = $classe->eleves->count();
        $moyenneGenerale = \App\Models\Note::where('enseignant_id', $enseignantId)
            ->whereHas('eleve', function($q) use ($id) {
                $q->where('classe_id', $id);
            })
            ->avg('note');
        
        $tauxPresence = \App\Models\Presence::whereHas('eleve', function($q) use ($id) {
            $q->where('classe_id', $id);
        })->whereMonth('date', now()->month)->count();
        
        $stats = [
            'total_eleves' => $totalEleves,
            'moyenne_generale' => round($moyenneGenerale ?: 0, 2),
            'taux_presence' => $totalEleves > 0 ? round(($tauxPresence / ($totalEleves * 20)) * 100, 2) : 0,
            'garcons' => $classe->eleves->where('sexe', 'M')->count(),
            'filles' => $classe->eleves->where('sexe', 'F')->count(),
        ];
        
        return view('enseignant.classes.show', compact('classe', 'emploiParJour', 'dernieresNotes', 'stats'));
    }
    
    /**
     * Affiche la liste des élèves d'une classe
     */
    public function eleves($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Vérifier que l'enseignant a accès à cette classe
        $aAcces = Classe::where('id', $id)
            ->where(function($q) use ($enseignantId) {
                $q->whereHas('emploisTemps', function($sub) use ($enseignantId) {
                    $sub->where('enseignant_id', $enseignantId);
                })->orWhere('professeur_principal_id', $enseignantId);
            })
            ->exists();
        
        if (!$aAcces) {
            return redirect()->route('enseignant.classes')
                ->with('error', 'Vous n\'avez pas accès à cette classe.');
        }
        
        $classe = Classe::findOrFail($id);
        
        $eleves = Eleve::where('classe_id', $id)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        
        // Pour chaque élève, récupérer ses moyennes par matière
        $matieres = $enseignant->matieres;
        $notesParEleve = [];
        
        foreach ($eleves as $eleve) {
            $notesParEleve[$eleve->id] = [
                'eleve' => $eleve,
                'moyennes' => []
            ];
            
            foreach ($matieres as $matiere) {
                $moyenne = \App\Models\Note::where('enseignant_id', $enseignantId)
                    ->where('matiere_id', $matiere->id)
                    ->where('eleve_id', $eleve->id)
                    ->avg('note');
                
                $notesParEleve[$eleve->id]['moyennes'][$matiere->id] = [
                    'matiere' => $matiere->nom,
                    'moyenne' => round($moyenne ?: 0, 2)
                ];
            }
        }
        
        return view('enseignant.classes.eleves', compact('classe', 'eleves', 'notesParEleve', 'matieres'));
    }
    
    /**
     * Exporte la liste des élèves d'une classe
     */
    public function export($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Vérifier que l'enseignant a accès à cette classe
        $aAcces = Classe::where('id', $id)
            ->where(function($q) use ($enseignantId) {
                $q->whereHas('emploisTemps', function($sub) use ($enseignantId) {
                    $sub->where('enseignant_id', $enseignantId);
                })->orWhere('professeur_principal_id', $enseignantId);
            })
            ->exists();
        
        if (!$aAcces) {
            return redirect()->route('enseignant.classes')
                ->with('error', 'Vous n\'avez pas accès à cette classe.');
        }
        
        $classe = Classe::findOrFail($id);
        $eleves = Eleve::where('classe_id', $id)
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        
        $filename = 'eleves_' . $classe->nom . '_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($handle, ['N°', 'Matricule', 'Nom', 'Prénom', 'Sexe', 'Date de naissance', 'Email', 'Téléphone']);
        
        foreach ($eleves as $index => $eleve) {
            fputcsv($handle, [
                $index + 1,
                $eleve->matricule ?? '',
                $eleve->nom,
                $eleve->prenom,
                $eleve->sexe ?? '',
                $eleve->date_naissance ? $eleve->date_naissance->format('d/m/Y') : '',
                $eleve->email ?? '',
                $eleve->telephone ?? ''
            ]);
        }
        
        fclose($handle);
        exit;
    }
}