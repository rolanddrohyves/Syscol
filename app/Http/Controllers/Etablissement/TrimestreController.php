<?php
// app/Http/Controllers/Etablissement/TrimestreController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Trimestre;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrimestreController extends Controller
{
    /**
     * Affiche le formulaire de création manuelle
     */
    public function create()
    {
        $anneesScolaires = AnneeScolaire::orderBy('libelle', 'desc')->get();
        
        return view('etablissement.trimestres.create', compact('anneesScolaires'));
    }

    /**
     * Crée manuellement un trimestre
     */
    public function createManual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'annee_scolaire_id' => 'required|exists:annees_scolaires,id',
            'numero' => 'required|integer|min:1|max:3',
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

        // Vérifier si un trimestre avec ce numéro existe déjà pour cette année
        $exists = Trimestre::where('annee_scolaire_id', $request->annee_scolaire_id)
            ->where('numero', $request->numero)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Un trimestre avec ce numéro existe déjà pour cette année scolaire.')
                ->withInput();
        }

        // Si on met ce trimestre comme courant, enlever le statut courant des autres
        if ($request->boolean('is_current')) {
            Trimestre::where('annee_scolaire_id', $request->annee_scolaire_id)
                ->update(['is_current' => false]);
        }

        Trimestre::create([
            'annee_scolaire_id' => $request->annee_scolaire_id,
            'numero' => $request->numero,
            'libelle' => $request->libelle,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'is_current' => $request->boolean('is_current'),
        ]);

        return redirect()->route('etablissement.trimestres.index')
            ->with('success', 'Trimestre créé avec succès.');
    }

    /**
     * Affiche la liste des trimestres
     */
    public function index()
    {
        $trimestres = Trimestre::with('anneeScolaire')
            ->orderBy('annee_scolaire_id', 'desc')
            ->orderBy('numero')
            ->paginate(15);
        
        return view('etablissement.trimestres.index', compact('trimestres'));
    }

    /**
     * Supprime un trimestre
     */
    public function destroy($id)
    {
        $trimestre = Trimestre::findOrFail($id);
        
        // Vérifier si des notes sont liées à ce trimestre
        if ($trimestre->notes()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer ce trimestre car il est lié à des notes.');
        }

        $trimestre->delete();

        return redirect()->route('etablissement.trimestres.index')
            ->with('success', 'Trimestre supprimé avec succès.');
    }
}