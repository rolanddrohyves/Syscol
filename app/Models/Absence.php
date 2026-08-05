<?php
// app/Models/Absence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    protected $fillable = [
        'eleve_id',
        'classe_id',
        'enseignant_id',
        'date',
        'heure',
        'motif',
        'justifiee',
        'justification',
        'type', // absence, retard, sortie_anticipée
    ];

    protected $casts = [
        'date' => 'date',
        'justifiee' => 'boolean',
    ];

    /**
     * Relation avec l'élève
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    /**
     * Relation avec la classe
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec l'enseignant qui a signalé
     */
    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    /**
     * Scope pour les absences non justifiées
     */
    public function scopeNonJustifiees($query)
    {
        return $query->where('justifiee', false);
    }

    /**
     * Scope pour les retards
     */
    public function scopeRetards($query)
    {
        return $query->where('type', 'retard');
    }

    /**
     * Scope pour une période donnée
     */
    public function scopeEntreDates($query, $debut, $fin)
    {
        return $query->whereBetween('date', [$debut, $fin]);
    }
}