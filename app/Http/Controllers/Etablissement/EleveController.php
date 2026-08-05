<?php
// app/Http/Controllers/Etablissement/EleveController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Classe;
use App\Models\User;
use App\Services\EcheanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EleveController extends Controller
{
    /**
     * Affiche la liste des élèves
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->withCount('eleves')
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $query = Eleve::whereHas('classe', function($q) use ($etablissementId) {
                $q->where('etablissement_id', $etablissementId);
            })
            ->with(['classe']);
        
        if ($request->filled('classe')) {
            $query->where('classe_id', $request->classe);
        }
        
        if ($request->filled('sexe')) {
            $query->where('sexe', $request->sexe);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%")
                  ->orWhere('nom_parent', 'like', "%{$search}%");
            });
        }
        
        $eleves = $query->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(15)
            ->appends($request->query());
        
        $stats = [
            'total' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count(),
            'actifs' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('status', 'actif')
                ->count(),
            'classes_count' => $classes->count(),
            'filles' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('sexe', 'F')
                ->count(),
            'garcons' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('sexe', 'M')
                ->count(),
        ];
        
        return view('etablissement.eleves.index', compact('eleves', 'classes', 'stats'));
    }

    /**
     * Affiche le formulaire de création d'un élève
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $classes = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        $classeId = $request->get('classe_id');
        $matricule = $this->generateMatricule();
        
        return view('etablissement.eleves.create', compact('classes', 'matricule', 'classeId'));
    }

    /**
     * Enregistre un nouvel élève
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $echeanceService = new EcheanceService();

        $validator = Validator::make($request->all(), [
            'matricule' => 'required|string|unique:eleves,matricule|max:20',
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:100',
            'sexe' => 'required|in:M,F',
            'adresse' => 'required|string|max:255',
            'telephone_parent' => 'required|string|max:20',
            'nom_parent' => 'required|string|max:100',
            'email_parent' => 'nullable|email|max:100',
            'classe_id' => 'required|exists:classes,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:actif,exclu,transferé,redoublant',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier que la classe appartient à l'établissement
        $classe = Classe::find($request->classe_id);
        if ($classe->etablissement_id != $user->etablissement_id) {
            return redirect()->back()
                ->with('error', 'La classe sélectionnée n\'appartient pas à votre établissement.')
                ->withInput();
        }

        // Vérifier la capacité de la classe
        $effectifActuel = Eleve::where('classe_id', $request->classe_id)->count();
        if ($effectifActuel >= $classe->capacite) {
            return redirect()->back()
                ->with('error', 'La classe a atteint sa capacité maximale (' . $classe->capacite . ' élèves).')
                ->withInput();
        }

        $data = $request->except('photo');
        $data['date_inscription'] = Carbon::now();
        $data['montant_total_frais'] = 0;
        $data['montant_paye'] = 0;
        $data['montant_restant'] = 0;
        
        // Gestion de la photo
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('eleves', 'public');
            $data['photo'] = $path;
        }

        // Créer l'élève
        $eleve = Eleve::create($data);
        
        // ✅ Générer les échéances pour l'élève
        try {
            $echeanceService->creerEcheancesPourEleve($eleve);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération des échéances: ' . $e->getMessage());
        }

        return redirect()->route('etablissement.eleves.index')
            ->with('success', 'Élève créé avec succès. Les échéances de paiement ont été générées.');
    }

    /**
     * Affiche les détails d'un élève
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $eleve = Eleve::with([
                'classe' => fn($q) => $q->with('etablissement'),
                'notes' => fn($q) => $q->with('matiere')->latest()->take(10),
                'absences' => fn($q) => $q->latest()->take(10)
            ])
            ->findOrFail($id);
        
        if ($eleve->classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cet élève.');
        }
        
        // Récupérer la situation financière via le service
        $echeanceService = new EcheanceService();
        $situationFinanciere = $echeanceService->getSituationFinanciere($eleve);
        
        $stats = [
            'age' => $eleve->date_naissance->age,
            'notes_count' => $eleve->notes()->count(),
            'absences_count' => $eleve->absences()->count(),
            'moyenne_generale' => $this->calculerMoyenneGenerale($eleve),
            'total_frais' => $situationFinanciere['total_general'],
            'total_paye' => $situationFinanciere['total_paye'],
            'total_reste' => $situationFinanciere['total_reste'],
            'pourcentage_paye' => $situationFinanciere['pourcentage_paye'],
        ];
        
        $dernieresNotes = $eleve->notes()
            ->with('matiere')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('etablissement.eleves.show', compact('eleve', 'stats', 'dernieresNotes', 'situationFinanciere'));
    }

    /**
     * Affiche le formulaire d'édition d'un élève
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        $eleve = Eleve::with('classe')->findOrFail($id);
        
        if ($eleve->classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cet élève.');
        }
        
        $classes = Classe::where('etablissement_id', $user->etablissement_id)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        return view('etablissement.eleves.edit', compact('eleve', 'classes'));
    }

    /**
     * Met à jour un élève
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $eleve = Eleve::with('classe')->findOrFail($id);
        
        if ($eleve->classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cet élève.');
        }

        $validator = Validator::make($request->all(), [
            'matricule' => 'required|string|unique:eleves,matricule,' . $id . '|max:20',
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:100',
            'sexe' => 'required|in:M,F',
            'adresse' => 'required|string|max:255',
            'telephone_parent' => 'required|string|max:20',
            'nom_parent' => 'required|string|max:100',
            'email_parent' => 'nullable|email|max:100',
            'classe_id' => 'required|exists:classes,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:actif,exclu,transferé,redoublant',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $nouvelleClasse = Classe::find($request->classe_id);
        if ($nouvelleClasse->etablissement_id != $user->etablissement_id) {
            return redirect()->back()
                ->with('error', 'La classe sélectionnée n\'appartient pas à votre établissement.')
                ->withInput();
        }

        // Si la classe change, régénérer les échéances
        $classeChangee = ($request->classe_id != $eleve->classe_id);
        
        if ($classeChangee) {
            $effectifNouvelleClasse = Eleve::where('classe_id', $request->classe_id)->count();
            if ($effectifNouvelleClasse >= $nouvelleClasse->capacite) {
                return redirect()->back()
                    ->with('error', 'La nouvelle classe a atteint sa capacité maximale (' . $nouvelleClasse->capacite . ' élèves).')
                    ->withInput();
            }
        }

        $data = $request->except('photo');
        
        if ($request->hasFile('photo')) {
            if ($eleve->photo) {
                Storage::disk('public')->delete($eleve->photo);
            }
            $path = $request->file('photo')->store('eleves', 'public');
            $data['photo'] = $path;
        }

        $eleve->update($data);
        
        // Si la classe a changé, régénérer les échéances
        if ($classeChangee) {
            $echeanceService = new EcheanceService();
            $echeanceService->creerEcheancesPourEleve($eleve);
        }

        return redirect()->route('etablissement.eleves.index')
            ->with('success', 'Élève mis à jour avec succès.');
    }

    /**
     * Supprime un élève
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $eleve = Eleve::with('classe')->findOrFail($id);
        
        if ($eleve->classe->etablissement_id != $user->etablissement_id) {
            abort(403, 'Vous n\'avez pas accès à cet élève.');
        }

        if ($eleve->photo) {
            Storage::disk('public')->delete($eleve->photo);
        }
        
        // Supprimer également les échéances (cascade)
        $eleve->delete();

        return redirect()->route('etablissement.eleves.index')
            ->with('success', 'Élève supprimé avec succès.');
    }

    /**
     * Affiche les notes d'un élève
     */
    public function notes($id)
    {
        $user = auth()->user();
        
        $eleve = Eleve::with(['classe', 'notes.matiere', 'notes.evaluation'])
            ->findOrFail($id);
        
        if ($eleve->classe->etablissement_id != $user->etablissement_id) {
            abort(403);
        }
        
        $notesParMatiere = $eleve->notes
            ->groupBy('matiere_id')
            ->map(function($notes, $matiereId) {
                $matiere = $notes->first()->matiere;
                $moyenne = $notes->avg('valeur');
                $coefficient = $matiere->coefficient ?? 1;
                
                return [
                    'matiere' => $matiere,
                    'notes' => $notes,
                    'moyenne' => round($moyenne, 2),
                    'coefficient' => $coefficient,
                    'total_points' => round($moyenne * $coefficient, 2)
                ];
            });
        
        $totalPoints = $notesParMatiere->sum('total_points');
        $totalCoefficients = $notesParMatiere->sum('coefficient');
        $moyenneGenerale = $totalCoefficients > 0 ? round($totalPoints / $totalCoefficients, 2) : 0;
        
        $stats = [
            'total_notes' => $eleve->notes->count(),
            'matieres_avec_notes' => $notesParMatiere->count(),
            'moyenne_generale' => $moyenneGenerale,
            'meilleure_note' => $eleve->notes->max('valeur'),
            'moins_bonne_note' => $eleve->notes->min('valeur'),
        ];
        
        return view('etablissement.eleves.notes', compact('eleve', 'notesParMatiere', 'stats'));
    }

    /**
     * Affiche la situation financière d'un élève
     */
    public function finances($id)
    {
        $user = auth()->user();
        $eleve = Eleve::with('classe')->findOrFail($id);
        
        if ($eleve->classe->etablissement_id != $user->etablissement_id) {
            abort(403);
        }
        
        $echeanceService = new EcheanceService();
        $situation = $echeanceService->getSituationFinanciere($eleve);
        
        return view('etablissement.eleves.finances', compact('eleve', 'situation'));
    }

    /**
     * Export de la liste des élèves
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'csv');
        
        $eleves = Eleve::whereHas('classe', function($q) use ($user) {
                $q->where('etablissement_id', $user->etablissement_id);
            })
            ->with('classe')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
        
        if ($format == 'csv') {
            return $this->exportCsv($eleves);
        }
        
        return redirect()->back()->with('error', 'Format non supporté');
    }

    /**
     * Export CSV
     */
    private function exportCsv($eleves)
    {
        $filename = 'eleves-' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['Matricule', 'Prénom', 'Nom', 'Date naissance', 'Sexe', 'Classe', 'Parent', 'Téléphone parent', 'Email parent', 'Statut', 'Total frais', 'Payé', 'Reste']);
        
        foreach ($eleves as $eleve) {
            fputcsv($handle, [
                $eleve->matricule,
                $eleve->prenom,
                $eleve->nom,
                $eleve->date_naissance->format('d/m/Y'),
                $eleve->sexe == 'F' ? 'Féminin' : 'Masculin',
                $eleve->classe->nom,
                $eleve->nom_parent,
                $eleve->telephone_parent,
                $eleve->email_parent,
                ucfirst($eleve->status),
                number_format($eleve->montant_total_frais, 0, ',', ' '),
                number_format($eleve->montant_paye, 0, ',', ' '),
                number_format($eleve->montant_restant, 0, ',', ' '),
            ]);
        }
        
        fclose($handle);
        exit;
    }

    /**
     * Statistiques des élèves (API)
     */
    public function statistiques()
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $stats = [
            'par_classe' => Classe::where('etablissement_id', $etablissementId)
                ->withCount('eleves')
                ->get()
                ->map(fn($classe) => [
                    'classe' => $classe->nom,
                    'effectif' => $classe->eleves_count,
                ]),
            'par_sexe' => [
                'filles' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                    ->where('sexe', 'F')
                    ->count(),
                'garcons' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                    ->where('sexe', 'M')
                    ->count(),
            ],
            'par_statut' => [
                'actifs' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                    ->where('status', 'actif')
                    ->count(),
                'exclus' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                    ->where('status', 'exclu')
                    ->count(),
                'transferes' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                    ->where('status', 'transferé')
                    ->count(),
                'redoublants' => Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                    ->where('status', 'redoublant')
                    ->count(),
            ],
        ];
        
        return response()->json($stats);
    }

    /**
     * Calcule la moyenne générale d'un élève
     */
    private function calculerMoyenneGenerale($eleve)
    {
        $notes = $eleve->notes()->with('matiere')->get();
        
        if ($notes->isEmpty()) {
            return 0;
        }
        
        $totalPoints = 0;
        $totalCoefficients = 0;
        
        foreach ($notes->groupBy('matiere_id') as $matiereId => $notesMatiere) {
            $matiere = $notesMatiere->first()->matiere;
            $moyenneMatiere = $notesMatiere->avg('valeur');
            $coefficient = $matiere->coefficient ?? 1;
            
            $totalPoints += $moyenneMatiere * $coefficient;
            $totalCoefficients += $coefficient;
        }
        
        return $totalCoefficients > 0 ? round($totalPoints / $totalCoefficients, 2) : 0;
    }

    /**
     * Génère un matricule unique
     */
    private function generateMatricule()
    {
        $prefix = 'ELE';
        $year = date('Y');
        $random = strtoupper(substr(uniqid(), -4));
        
        $matricule = $prefix . $year . $random;
        
        while (Eleve::where('matricule', $matricule)->exists()) {
            $random = strtoupper(substr(uniqid(), -4));
            $matricule = $prefix . $year . $random;
        }
        
        return $matricule;
    }
}