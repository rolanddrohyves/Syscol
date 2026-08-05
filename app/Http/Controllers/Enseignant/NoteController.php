<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Eleve;
use App\Models\Note;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NoteController extends Controller
{
    /**
     * Affiche la liste des notes par classe et matière
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Récupérer les classes de l'enseignant
        $classes = Classe::whereHas('emploisTemps', function($q) use ($enseignantId) {
            $q->where('enseignant_id', $enseignantId);
        })->get();
        
        // Récupérer les matières de l'enseignant
        $matieres = $enseignant->matieres;
        
        $classeId = $request->get('classe_id');
        $matiereId = $request->get('matiere_id');
        
        $notes = collect();
        $selectedClasse = null;
        $selectedMatiere = null;
        
        if ($classeId && $matiereId) {
            $selectedClasse = Classe::find($classeId);
            $selectedMatiere = Matiere::find($matiereId);
            
            $notes = Note::where('enseignant_id', $enseignantId)
                ->where('matiere_id', $matiereId)
                ->whereHas('eleve', function($q) use ($classeId) {
                    $q->where('classe_id', $classeId);
                })
                ->with(['eleve', 'matiere'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('enseignant.notes.index', compact('classes', 'matieres', 'notes', 'selectedClasse', 'selectedMatiere', 'classeId', 'matiereId'));
    }
    
    /**
     * Affiche le formulaire de création de notes pour une classe et matière
     */
    public function create($classeId, $matiereId)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        // Vérifier que l'enseignant enseigne bien cette matière dans cette classe
        $enseigne = DB::table('emplois_temps')
            ->where('enseignant_id', $enseignantId)
            ->where('classe_id', $classeId)
            ->where('matiere_id', $matiereId)
            ->exists();
        
        if (!$enseigne) {
            return redirect()->route('enseignant.notes')
                ->with('error', 'Vous n\'êtes pas autorisé à saisir des notes pour cette classe et cette matière.');
        }
        
        $classe = Classe::with('eleves')->findOrFail($classeId);
        $matiere = Matiere::findOrFail($matiereId);
        
        // Récupérer les notes existantes pour cette classe/matière
        $notesExistantes = Note::where('enseignant_id', $enseignantId)
            ->where('classe_id', $classeId)
            ->where('matiere_id', $matiereId)
            ->get()
            ->keyBy('eleve_id');
        
        return view('enseignant.notes.create', compact('classe', 'matiere', 'notesExistantes'));
    }
    
    /**
     * Enregistre les notes pour une classe et matière
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        
        $request->validate([
            'classe_id' => 'required|exists:classes,id',
            'matiere_id' => 'required|exists:matieres,id',
            'notes' => 'required|array',
            'notes.*.eleve_id' => 'required|exists:eleves,id',
            'notes.*.note' => 'nullable|numeric|min:0|max:20',
            'notes.*.appreciation' => 'nullable|string|max:255',
        ]);
        
        $classeId = $request->classe_id;
        $matiereId = $request->matiere_id;
        
        // Vérifier que l'enseignant enseigne bien cette matière dans cette classe
        $enseigne = DB::table('emplois_temps')
            ->where('enseignant_id', $enseignantId)
            ->where('classe_id', $classeId)
            ->where('matiere_id', $matiereId)
            ->exists();
        
        if (!$enseigne) {
            return redirect()->route('enseignant.notes')
                ->with('error', 'Vous n\'êtes pas autorisé à saisir des notes pour cette classe et cette matière.');
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($request->notes as $noteData) {
                if (isset($noteData['note']) && $noteData['note'] !== '') {
                    Note::updateOrCreate(
                        [
                            'eleve_id' => $noteData['eleve_id'],
                            'matiere_id' => $matiereId,
                            'enseignant_id' => $enseignantId,
                            'trimestre_id' => $request->trimestre_id ?? null,
                        ],
                        [
                            'note' => $noteData['note'],
                            'note_max' => 20,
                            'appreciation' => $noteData['appreciation'] ?? null,
                            'date_evaluation' => $request->date_evaluation ?? Carbon::now(),
                            'classe_id' => $classeId,
                        ]
                    );
                }
            }
            
            DB::commit();
            
            return redirect()->route('enseignant.notes', ['classe_id' => $classeId, 'matiere_id' => $matiereId])
                ->with('success', 'Les notes ont été enregistrées avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des notes : ' . $e->getMessage());
        }
    }
    
    /**
     * Affiche le formulaire d'édition d'une note
     */
    public function edit($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $note = Note::with(['eleve', 'matiere'])->findOrFail($id);
        
        // Vérifier que la note appartient bien à l'enseignant
        if ($note->enseignant_id != $enseignant->user_id) {
            return redirect()->route('enseignant.notes')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette note.');
        }
        
        return view('enseignant.notes.edit', compact('note'));
    }
    
    /**
     * Met à jour une note
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $request->validate([
            'note' => 'required|numeric|min:0|max:20',
            'appreciation' => 'nullable|string|max:255',
        ]);
        
        $note = Note::findOrFail($id);
        
        // Vérifier que la note appartient bien à l'enseignant
        if ($note->enseignant_id != $enseignant->user_id) {
            return redirect()->route('enseignant.notes')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette note.');
        }
        
        $note->update([
            'note' => $request->note,
            'appreciation' => $request->appreciation,
        ]);
        
        return redirect()->route('enseignant.notes', [
            'classe_id' => $note->classe_id,
            'matiere_id' => $note->matiere_id
        ])->with('success', 'La note a été modifiée avec succès.');
    }
    
    /**
     * Supprime une note
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $note = Note::findOrFail($id);
        
        // Vérifier que la note appartient bien à l'enseignant
        if ($note->enseignant_id != $enseignant->user_id) {
            return redirect()->route('enseignant.notes')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette note.');
        }
        
        $classeId = $note->classe_id;
        $matiereId = $note->matiere_id;
        
        $note->delete();
        
        return redirect()->route('enseignant.notes', [
            'classe_id' => $classeId,
            'matiere_id' => $matiereId
        ])->with('success', 'La note a été supprimée avec succès.');
    }
    
    /**
     * Exporte les notes d'une classe/matière en CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Profil enseignant non trouvé.');
        }
        
        $enseignantId = $enseignant->user_id;
        $classeId = $request->get('classe_id');
        $matiereId = $request->get('matiere_id');
        
        if (!$classeId || !$matiereId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner une classe et une matière.');
        }
        
        $classe = Classe::find($classeId);
        $matiere = Matiere::find($matiereId);
        
        $notes = Note::where('enseignant_id', $enseignantId)
            ->where('matiere_id', $matiereId)
            ->whereHas('eleve', function($q) use ($classeId) {
                $q->where('classe_id', $classeId);
            })
            ->with(['eleve', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($notes->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune note à exporter.');
        }
        
        $filename = 'notes_' . $classe->nom . '_' . $matiere->nom . '_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($handle, ['N°', 'Matricule', 'Nom', 'Prénom', 'Note', 'Appréciation', 'Date de saisie']);
        
        foreach ($notes as $index => $note) {
            fputcsv($handle, [
                $index + 1,
                $note->eleve->matricule ?? '',
                $note->eleve->nom,
                $note->eleve->prenom,
                $note->note . '/20',
                $note->appreciation ?? '',
                $note->created_at->format('d/m/Y H:i')
            ]);
        }
        
        fclose($handle);
        exit;
    }
    
    /**
     * Récupère les statistiques des notes pour les graphiques
     */
    public function statistiques(Request $request)
    {
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return response()->json(['error' => 'Enseignant non trouvé'], 404);
        }
        
        $enseignantId = $enseignant->user_id;
        $classeId = $request->get('classe_id');
        $matiereId = $request->get('matiere_id');
        
        $query = Note::where('enseignant_id', $enseignantId);
        
        if ($classeId) {
            $query->whereHas('eleve', function($q) use ($classeId) {
                $q->where('classe_id', $classeId);
            });
        }
        
        if ($matiereId) {
            $query->where('matiere_id', $matiereId);
        }
        
        $notes = $query->get();
        
        $stats = [
            'total_notes' => $notes->count(),
            'moyenne' => round($notes->avg('note'), 2),
            'min' => $notes->min('note'),
            'max' => $notes->max('note'),
            'repartition' => [
                'excellent' => $notes->where('note', '>=', 16)->count(), // >= 16
                'tres_bien' => $notes->whereBetween('note', [14, 15.99])->count(), // 14-15.99
                'bien' => $notes->whereBetween('note', [12, 13.99])->count(), // 12-13.99
                'passable' => $notes->whereBetween('note', [10, 11.99])->count(), // 10-11.99
                'insuffisant' => $notes->whereBetween('note', [8, 9.99])->count(), // 8-9.99
                'faible' => $notes->where('note', '<', 8)->count(), // < 8
            ]
        ];
        
        return response()->json($stats);
    }
}