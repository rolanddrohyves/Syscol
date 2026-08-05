<?php
// app/Models/Trimestre.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trimestre extends Model
{
    protected $fillable = [
        'libelle',
        'numero',
        'date_debut',
        'date_fin',
        'is_current',
        'annee_scolaire_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'is_current' => 'boolean',
    ];

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}