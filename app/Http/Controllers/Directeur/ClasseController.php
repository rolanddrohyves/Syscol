<?php
// app/Http/Controllers/Directeur/ClasseController.php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Inscription;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    /**
     * Affiche la liste des classes
     */
    public function index()
    {
        $classes = Classe::where('etablissement_id', auth()->user()->etablissement_id)
            ->with(['niveau', 'anneeScolaire'])
            ->get();

        return view('directeur.classes.index', compact('classes'));
    }

    /**
     * Affiche les détails d'une classe
     */
    public function show($id)
    {
        $classe = Classe::with(['niveau', 'anneeScolaire', 'inscriptions.etudiant'])
            ->findOrFail($id);

        $effectifs = Inscription::where('classe_id', $id)
            ->where('statut', 'active')
            ->count();

        return view('directeur.classes.show', compact('classe', 'effectifs'));
    }
}