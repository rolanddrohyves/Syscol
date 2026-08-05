<?php
// app/Models/Presence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    protected $table = 'presences';
    
    protected $fillable = [
        'eleve_id',
        'classe_id',
        'date',
        'statut',
        'justifiee',
        'motif',
        'enseignant_id',
        'annee_scolaire_id',
        'trimestre_id',
        'heure_arrivee',
        'heure_depart',
    ];

    protected $casts = [
        'date' => 'date',
        'justifiee' => 'boolean',
        'heure_arrivee' => 'datetime',
        'heure_depart' => 'datetime',
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
     * Relation avec l'enseignant
     */
    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(Enseignant::class, 'enseignant_id', 'user_id');
    }

    /**
     * Relation avec l'année scolaire
     */
    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    /**
     * Relation avec le trimestre
     */
    public function trimestre(): BelongsTo
    {
        return $this->belongsTo(Trimestre::class);
    }

    /**
     * Accesseur pour savoir si l'élève est présent
     */
    public function getEstPresentAttribute(): bool
    {
        return $this->statut === 'present';
    }

    /**
     * Accesseur pour savoir si l'élève est absent
     */
    public function getEstAbsentAttribute(): bool
    {
        return $this->statut === 'absent';
    }

    /**
     * Accesseur pour savoir si l'élève est en retard
     */
    public function getEstRetardAttribute(): bool
    {
        return $this->statut === 'retard';
    }

    /**
     * Scope pour les présences du jour
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope pour les présences d'une classe
     */
    public function scopePourClasse($query, $classeId)
    {
        return $query->where('classe_id', $classeId);
    }

    /**
     * Scope pour les présences d'un élève
     */
    public function scopePourEleve($query, $eleveId)
    {
        return $query->where('eleve_id', $eleveId);
    }

    /**
     * Scope pour les présences d'une période
     */
    public function scopeEntreDates($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween('date', [$dateDebut, $dateFin]);
    }
}