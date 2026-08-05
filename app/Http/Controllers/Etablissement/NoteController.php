<?php
// app/Http/Controllers/Etablissement/NoteController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Eleve;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Trimestre; // ✅ Importer Trimestre au lieu de AnneeScolaire
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * Affiche la liste des notes
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe', 'matiere', 'enseignant', 'trimestre']); // ✅ Ajouter trimestre
        
        // Filtres
        if ($request->filled('classe_id')) {
            $query->whereHas('eleve', fn($q) => $q->where('classe_id', $request->classe_id));
        }
        
        if ($request->filled('matiere_id')) {
            $query->where('matiere_id', $request->matiere_id);
        }
        
        if ($request->filled('trimestre_id')) {
            $query->where('trimestre_id', $request->trimestre_id);
        }
        
        $notes = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $classes = Classe::where('etablissement_id', $etablissementId)->get();
        $matieres = Matiere::all();
        
        // ✅ Récupérer les trimestres de l'année en cours
        $trimestres = Trimestre::whereHas('anneeScolaire', function($q) {
                $q->where('is_current', true);
            })
            ->orderBy('numero')
            ->get();
        
        return view('etablissement.notes.index', compact('notes', 'classes', 'matieres', 'trimestres'));
    }

    /**
     * Affiche le formulaire de création d'une note
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $eleveId = $request->get('eleve_id');
        $classeId = $request->get('classe_id');
        
        // Récupérer toutes les classes de l'établissement
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        // Préparer les élèves par classe pour le JavaScript
        $elevesParClasse = [];
        foreach ($classes as $classe) {
            $elevesParClasse[$classe->id] = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get(['id', 'prenom', 'nom']);
        }
        
        $matieres = Matiere::all();
        
        // ✅ Récupérer les TRIMESTRES (pas les années scolaires)
        $trimestres = Trimestre::whereHas('anneeScolaire', function($q) {
                $q->where('is_current', true); // Année scolaire en cours
            })
            ->orderBy('numero')
            ->get();
        
        return view('etablissement.notes.create', compact(
            'classes',
            'elevesParClasse',
            'matieres',
            'trimestres',
            'eleveId',
            'classeId'
        ));
    }

    /**
     * Enregistre une note
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'trimestre_id' => 'required|exists:trimestres,id', // ✅ Vérifier dans trimestres
            'note' => 'required|numeric|min:0|max:20',
            'note_max' => 'required|numeric|min:1|max:20',
            'appreciation' => 'nullable|string|max:255',
            'date_evaluation' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que l'élève existe
        $eleve = Eleve::with('classe')->find($request->eleve_id);
        if (!$eleve) {
            return redirect()->back()
                ->with('error', 'Élève introuvable.')
                ->withInput();
        }

        Note::create([
            'eleve_id' => $request->eleve_id,
            'matiere_id' => $request->matiere_id,
            'enseignant_id' => auth()->id(),
            'trimestre_id' => $request->trimestre_id,
            'note' => $request->note,
            'note_max' => $request->note_max,
            'appreciation' => $request->appreciation,
            'date_evaluation' => $request->date_evaluation,
        ]);

        return redirect()->route('etablissement.notes.index')
            ->with('success', 'Note ajoutée avec succès.');
    }

    /**
     * Affiche les détails d'une note
     */
    public function show($id)
    {
        $note = Note::with(['eleve', 'eleve.classe', 'matiere', 'enseignant', 'trimestre'])->findOrFail($id);
        
        return view('etablissement.notes.show', compact('note'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $note = Note::findOrFail($id);
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $elevesParClasse = [];
        foreach ($classes as $classe) {
            $elevesParClasse[$classe->id] = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get(['id', 'prenom', 'nom']);
        }
        
        $matieres = Matiere::all();
        
        // ✅ Récupérer les trimestres
        $trimestres = Trimestre::whereHas('anneeScolaire', function($q) {
                $q->where('is_current', true);
            })
            ->orderBy('numero')
            ->get();
        
        return view('etablissement.notes.edit', compact('note', 'classes', 'elevesParClasse', 'matieres', 'trimestres'));
    }

    /**
     * Met à jour une note
     */
    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'matiere_id' => 'required|exists:matieres,id',
            'trimestre_id' => 'required|exists:trimestres,id', // ✅ Vérifier dans trimestres
            'note' => 'required|numeric|min:0|max:20',
            'note_max' => 'required|numeric|min:1|max:20',
            'appreciation' => 'nullable|string|max:255',
            'date_evaluation' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $note->update([
            'eleve_id' => $request->eleve_id,
            'matiere_id' => $request->matiere_id,
            'trimestre_id' => $request->trimestre_id,
            'note' => $request->note,
            'note_max' => $request->note_max,
            'appreciation' => $request->appreciation,
            'date_evaluation' => $request->date_evaluation,
        ]);

        return redirect()->route('etablissement.notes.index')
            ->with('success', 'Note mise à jour avec succès.');
    }

    /**
     * Supprime une note
     */
    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();

        return redirect()->route('etablissement.notes.index')
            ->with('success', 'Note supprimée avec succès.');
    }

    /**
     * Export des notes
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $notes = Note::whereHas('eleve.classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['eleve', 'eleve.classe', 'matiere', 'trimestre'])
            ->orderBy('date_evaluation', 'desc')
            ->get();
        
        $filename = 'notes-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['Date', 'Élève', 'Classe', 'Matière', 'Note', 'Note max', 'Appréciation', 'Trimestre']);
        
        foreach ($notes as $note) {
            fputcsv($handle, [
                $note->date_evaluation->format('d/m/Y'),
                $note->eleve->prenom . ' ' . $note->eleve->nom,
                $note->eleve->classe->nom,
                $note->matiere->nom,
                $note->note,
                $note->note_max,
                $note->appreciation,
                $note->trimestre->libelle ?? 'N/A', 
            ]);
        }
        
        fclose($handle);
        exit;
    }
}