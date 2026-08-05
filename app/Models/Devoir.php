<?php
// app/Models/Devoir.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devoir extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'matiere_id',
        'classe_id',
        'enseignant_id',
        'date_limite',
        'fichier',
        'statut'
    ];

    protected $casts = [
        'date_limite' => 'datetime',
    ];

    // Relation avec la matière
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    // Relation avec la classe
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    // Relation avec l'enseignant
    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }
}