<?php
// app/Models/Sanction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sanction extends Model
{
    protected $fillable = [
        'eleve_id',
        'type',
        'date',
        'motif',
        'description',
        'duree',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'date' => 'date',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Relation avec l'élève
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }
}