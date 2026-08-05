<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    /**
     * Affiche la liste des évaluations
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Récupérer les classes et matières pour les filtres
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->get();
        
        $matieres = $enseignant->matieres;
        
        // Filtres
        $classeId = $request->get('classe_id');
        $matiereId = $request->get('matiere_id');
        $statut = $request->get('statut');
        
        $query = Evaluation::where('enseignant_id', $enseignantId)
            ->with(['classe', 'matiere']);
        
        if ($classeId) {
            $query->where('classe_id', $classeId);
        }
        
        if ($matiereId) {
            $query->where('matiere_id', $matiereId);
        }
        
        if ($statut === 'passe') {
            $query->where('date_evaluation', '<', Carbon::now());
        } elseif ($statut === 'a_venir') {
            $query->where('date_evaluation', '>=', Carbon::now());
        } elseif ($statut === 'non_note') {
            $query->where('date_evaluation', '<', Carbon::now())
                  ->whereDoesntHave('notes');
        }
        
        $evaluations = $query->orderBy('date_evaluation', 'desc')->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => Evaluation::where('enseignant_id', $enseignantId)->count(),
            'a_venir' => Evaluation::where('enseignant_id', $enseignantId)
                ->where('date_evaluation', '>=', Carbon::now())
                ->count(),
            'non_notees' => Evaluation::where('enseignant_id', $enseignantId)
                ->where('date_evaluation', '<', Carbon::now())
                ->whereDoesntHave('notes')
                ->count(),
        ];
        
        return view('enseignant.evaluations.index', compact('evaluations', 'classes', 'matieres', 'stats', 'classeId', 'matiereId', 'statut'));
    }
    
    /**
     * Affiche le formulaire de création d'évaluation
     */
    public function create()
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Récupérer les classes et matières de l'enseignant
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->get();
        
        $matieres = $enseignant->matieres;
        
        return view('enseignant.evaluations.create', compact('classes', 'matieres'));
    }
    
    /**
     * Enregistre une nouvelle évaluation
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_evaluation' => 'required|date',
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type' => 'required|in:devoir,composition,examen',
        ]);
        
        // Vérifier que l'enseignant enseigne bien cette matière dans cette classe
        $enseigne = DB::table('emplois_temps')
            ->where('enseignant_id', $enseignant->user_id)
            ->where('classe_id', $request->classe_id)
            ->where('matiere_id', $request->matiere_id)
            ->exists();
        
        if (!$enseigne) {
            return redirect()->back()
                ->with('error', 'Vous n\'êtes pas autorisé à créer une évaluation pour cette classe et cette matière.')
                ->withInput();
        }
        
        Evaluation::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'date_evaluation' => $request->date_evaluation,
            'coefficient' => $request->coefficient,
            'classe_id' => $request->classe_id,
            'matiere_id' => $request->matiere_id,
            'enseignant_id' => $enseignant->user_id,
            'type' => $request->type,
        ]);
        
        return redirect()->route('enseignant.evaluations.index')
            ->with('success', 'L\'évaluation a été créée avec succès.');
    }
    
    /**
     * Affiche les détails d'une évaluation
     */
    public function show($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $evaluation = Evaluation::with(['classe', 'matiere', 'notes.eleve'])
            ->where('enseignant_id', $enseignant->user_id)
            ->findOrFail($id);
        
        // Statistiques des notes
        $notes = $evaluation->notes;
        $stats = [
            'total_eleves' => $evaluation->classe->eleves->count(),
            'notes_saisies' => $notes->count(),
            'moyenne' => round($notes->avg('valeur'), 2),
            'min' => $notes->min('valeur'),
            'max' => $notes->max('valeur'),
            'taux_saisie' => $evaluation->classe->eleves->count() > 0 
                ? round(($notes->count() / $evaluation->classe->eleves->count()) * 100, 2) 
                : 0,
        ];
        
        return view('enseignant.evaluations.show', compact('evaluation', 'stats'));
    }
    
    /**
     * Affiche le formulaire d'édition d'une évaluation
     */
    public function edit($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $evaluation = Evaluation::where('enseignant_id', $enseignant->user_id)
            ->findOrFail($id);
        
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignant) {
            $q->where('enseignant_id', $enseignant->user_id);
        })->get();
        
        $matieres = $enseignant->matieres;
        
        return view('enseignant.evaluations.edit', compact('evaluation', 'classes', 'matieres'));
    }
    
    /**
     * Met à jour une évaluation
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $evaluation = Evaluation::where('enseignant_id', $enseignant->user_id)
            ->findOrFail($id);
        
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_evaluation' => 'required|date',
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'type' => 'required|in:devoir,composition,examen',
        ]);
        
        $evaluation->update($request->all());
        
        return redirect()->route('enseignant.evaluations.index')
            ->with('success', 'L\'évaluation a été modifiée avec succès.');
    }
    
    /**
     * Supprime une évaluation
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $evaluation = Evaluation::where('enseignant_id', $enseignant->user_id)
            ->findOrFail($id);
        
        // Supprimer les notes associées
        $evaluation->notes()->delete();
        $evaluation->delete();
        
        return redirect()->route('enseignant.evaluations.index')
            ->with('success', 'L\'évaluation a été supprimée avec succès.');
    }
    
    /**
     * Saisie des notes pour une évaluation
     */
    public function saisieNotes($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $evaluation = Evaluation::with(['classe.eleves', 'matiere'])
            ->where('enseignant_id', $enseignant->user_id)
            ->findOrFail($id);
        
        $notesExistantes = Note::where('evaluation_id', $id)
            ->get()
            ->keyBy('eleve_id');
        
        return view('enseignant.evaluations.notes', compact('evaluation', 'notesExistantes'));
    }
    
    /**
     * Enregistre les notes d'une évaluation
     */
    public function storeNotes(Request $request, $id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $evaluation = Evaluation::where('enseignant_id', $enseignant->user_id)
            ->findOrFail($id);
        
        $request->validate([
            'notes' => 'required|array',
            'notes.*.eleve_id' => 'required|exists:eleves,id',
            'notes.*.valeur' => 'nullable|numeric|min:0|max:20',
            'notes.*.appreciation' => 'nullable|string|max:255',
        ]);
        
        DB::beginTransaction();
        
        try {
            foreach ($request->notes as $noteData) {
                if (isset($noteData['valeur']) && $noteData['valeur'] !== '') {
                    Note::updateOrCreate(
                        [
                            'evaluation_id' => $evaluation->id,
                            'eleve_id' => $noteData['eleve_id'],
                        ],
                        [
                            'valeur' => $noteData['valeur'],
                            'appreciation' => $noteData['appreciation'] ?? null,
                        ]
                    );
                }
            }
            
            DB::commit();
            
            return redirect()->route('enseignant.evaluations.show', $evaluation->id)
                ->with('success', 'Les notes ont été enregistrées avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des notes.');
        }
    }
}