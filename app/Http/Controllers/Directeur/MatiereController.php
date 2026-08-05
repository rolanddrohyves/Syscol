<?php
// app/Http/Controllers/Directeur/MatiereController.php

namespace App\Http\Controllers\Directeur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    /**
     * Affiche la liste des matières
     */
    public function index()
    {
        return view('directeur.matieres.index');
    }
}