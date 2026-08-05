<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnneeScolaire extends Model
{
    protected $table = 'annees_scolaires';
    
    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'is_current',
        'etablissement_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Relation avec les trimestres
     */
    public function trimestres(): HasMany
    {
        return $this->hasMany(Trimestre::class);
    }

    /**
     * Relation avec l'établissement
     */
    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }
}