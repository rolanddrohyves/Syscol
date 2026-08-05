<?php
// app/Models/Discipline.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discipline extends Model
{
    protected $table = 'disciplines';
    
    protected $fillable = [
        'eleve_id',
        'type',
        'gravite',
        'date',
        'heure',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relation avec l'élève
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    /**
     * Scope pour filtrer par gravité
     */
    public function scopeGravite($query, $gravite)
    {
        return $query->where('gravite', $gravite);
    }

    /**
     * Scope pour la période
     */
    public function scopeEntreDates($query, $debut, $fin)
    {
        return $query->whereBetween('date', [$debut, $fin]);
    }

    /**
     * Scope pour aujourd'hui
     */
    public function scopeAujourdhui($query)
    {
        return $query->whereDate('date', now());
    }

    /**
     * Scope pour cette semaine
     */
    public function scopeCetteSemaine($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope pour ce mois
     */
    public function scopeCeMois($query)
    {
        return $query->whereMonth('date', now()->month);
    }

    /**
     * Récupère le libellé de la gravité
     */
    public function getGraviteLabelAttribute(): string
    {
        return match($this->gravite) {
            'faible' => 'Faible',
            'moyenne' => 'Moyenne',
            'elevee' => 'Élevée',
            'critique' => 'Critique',
            default => ucfirst($this->gravite),
        };
    }

    /**
     * Récupère la couleur selon la gravité
     */
    public function getGraviteColorAttribute(): string
    {
        return match($this->gravite) {
            'faible' => 'green',
            'moyenne' => 'yellow',
            'elevee' => 'orange',
            'critique' => 'red',
            default => 'gray',
        };
    }

    /**
     * Récupère le libellé du type
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'incident' => 'Incident',
            'avertissement' => 'Avertissement',
            'retenue' => 'Retenue',
            'exclusion' => 'Exclusion',
            default => ucfirst($this->type),
        };
    }
}