<?php
// app/Http/Controllers/Etablissement/AnneeScolaireController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\AnneeScolaire;
use App\Models\Trimestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AnneeScolaireController extends Controller
{
    /**
     * Affiche la liste des années scolaires
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $query = AnneeScolaire::where('etablissement_id', $etablissementId)
            ->withCount('trimestres');
        
        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('libelle', 'like', "%{$search}%");
        }
        
        $anneesScolaires = $query->orderBy('date_debut', 'desc')->paginate(15);
        
        // Statistiques
        $stats = [
            'total' => AnneeScolaire::where('etablissement_id', $etablissementId)->count(),
            'en_cours' => AnneeScolaire::where('etablissement_id', $etablissementId)
                ->where('is_current', true)
                ->count(),
            'terminees' => AnneeScolaire::where('etablissement_id', $etablissementId)
                ->where('is_current', false)
                ->where('date_fin', '<', now())
                ->count(),
            'a_venir' => AnneeScolaire::where('etablissement_id', $etablissementId)
                ->where('date_debut', '>', now())
                ->count(),
        ];
        
        return view('etablissement.annes-scolaires.index', compact('anneesScolaires', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('etablissement.annes-scolaires.create');
    }

    /**
     * Enregistre une nouvelle année scolaire
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'is_current' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Si on marque cette année comme courante, enlever le statut des autres
        if ($request->boolean('is_current')) {
            AnneeScolaire::where('etablissement_id', $user->etablissement_id)
                ->update(['is_current' => false]);
        }

        $anneeScolaire = AnneeScolaire::create([
            'libelle' => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'is_current' => $request->boolean('is_current', false),
            'etablissement_id' => $user->etablissement_id,
        ]);

        // Créer automatiquement les 3 trimestres
        $this->creerTrimestres($anneeScolaire);

        return redirect()->route('etablissement.annes_scolaires.index')
            ->with('success', 'Année scolaire créée avec succès.');
    }

    /**
     * Affiche les détails d'une année scolaire
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->with('trimestres')
            ->findOrFail($id);
        
        $stats = [
            'total_trimestres' => $anneeScolaire->trimestres->count(),
            'trimestre_en_cours' => $anneeScolaire->trimestres->where('is_current', true)->first(),
            'duree_jours' => Carbon::parse($anneeScolaire->date_debut)->diffInDays($anneeScolaire->date_fin),
            'est_terminee' => Carbon::parse($anneeScolaire->date_fin)->isPast(),
        ];
        
        return view('etablissement.annes-scolaires.show', compact('anneeScolaire', 'stats'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->findOrFail($id);
        
        return view('etablissement.annes-scolaires.edit', compact('anneeScolaire'));
    }

    /**
     * Met à jour une année scolaire
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'is_current' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Si on marque cette année comme courante, enlever le statut des autres
        if ($request->boolean('is_current') && !$anneeScolaire->is_current) {
            AnneeScolaire::where('etablissement_id', $user->etablissement_id)
                ->where('id', '!=', $id)
                ->update(['is_current' => false]);
        }

        $anneeScolaire->update([
            'libelle' => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'is_current' => $request->boolean('is_current', false),
        ]);

        return redirect()->route('etablissement.annes_scolaires.index')
            ->with('success', 'Année scolaire mise à jour avec succès.');
    }

    /**
     * Supprime une année scolaire
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->findOrFail($id);
        
        // Vérifier s'il y a des trimestres liés
        if ($anneeScolaire->trimestres()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette année scolaire car elle contient des trimestres.');
        }

        $anneeScolaire->delete();

        return redirect()->route('etablissement.annes_scolaires.index')
            ->with('success', 'Année scolaire supprimée avec succès.');
    }

    /**
     * Définit l'année scolaire courante
     */
    public function setCurrent($id)
    {
        $user = auth()->user();
        
        $anneeScolaire = AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->findOrFail($id);
        
        // Enlever le statut courant de toutes les années
        AnneeScolaire::where('etablissement_id', $user->etablissement_id)
            ->update(['is_current' => false]);
        
        // Mettre à jour l'année sélectionnée
        $anneeScolaire->update(['is_current' => true]);
        
        // Mettre à jour le premier trimestre comme courant
        Trimestre::where('annee_scolaire_id', $id)
            ->where('numero', 1)
            ->update(['is_current' => true]);
        
        Trimestre::where('annee_scolaire_id', $id)
            ->where('numero', '>', 1)
            ->update(['is_current' => false]);

        return redirect()->back()
            ->with('success', 'Année scolaire définie comme courante.');
    }

    /**
     * Crée automatiquement les 3 trimestres pour une année scolaire
     */
    private function creerTrimestres($anneeScolaire)
    {
        $debutAnnee = Carbon::parse($anneeScolaire->date_debut);
        $finAnnee = Carbon::parse($anneeScolaire->date_fin);
        
        $dureeTotale = $debutAnnee->diffInDays($finAnnee);
        $dureeTrimestre = floor($dureeTotale / 3);

        $trimestres = [
            [
                'numero' => 1,
                'libelle' => 'Trimestre 1',
                'date_debut' => $debutAnnee->copy(),
                'date_fin' => $debutAnnee->copy()->addDays($dureeTrimestre),
                'is_current' => $anneeScolaire->is_current ? true : false,
            ],
            [
                'numero' => 2,
                'libelle' => 'Trimestre 2',
                'date_debut' => $debutAnnee->copy()->addDays($dureeTrimestre + 1),
                'date_fin' => $debutAnnee->copy()->addDays($dureeTrimestre * 2),
                'is_current' => false,
            ],
            [
                'numero' => 3,
                'libelle' => 'Trimestre 3',
                'date_debut' => $debutAnnee->copy()->addDays($dureeTrimestre * 2 + 1),
                'date_fin' => $finAnnee,
                'is_current' => false,
            ],
        ];

        foreach ($trimestres as $trimestre) {
            Trimestre::create([
                'libelle' => $trimestre['libelle'],
                'numero' => $trimestre['numero'],
                'date_debut' => $trimestre['date_debut'],
                'date_fin' => $trimestre['date_fin'],
                'is_current' => $trimestre['is_current'],
                'annee_scolaire_id' => $anneeScolaire->id,
            ]);
        }
    }
}