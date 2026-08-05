<?php
// app/Models/Eleve.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Eleve extends Model
{
    protected $fillable = [
        'classe_id',
        'parent_id',  
        'matricule',
        'prenom',
        'nom',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'adresse',
        'telephone_parent',
        'nom_parent',
        'email_parent',
        'photo',
        'status',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Relation avec la classe
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * RELATION AVEC LES NOTES
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * RELATION AVEC LES ABSENCES
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * RELATION AVEC LES DISCIPLINES
     */
    public function disciplines(): HasMany
    {
        return $this->hasMany(Discipline::class);
    }

    /**
     * RELATION AVEC LES PAIEMENTS (AJOUTÉE POUR LE COMPTABLE)
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Nom complet de l'élève
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Âge de l'élève
     */
    public function getAgeAttribute(): int
    {
        return $this->date_naissance->age;
    }

    /**
     * Moyenne générale de l'élève
     */
    public function moyenneGenerale(?int $trimestreId = null): ?float
    {
        $query = $this->notes();
        
        if ($trimestreId) {
            $query->where('trimestre_id', $trimestreId);
        }
        
        $notes = $query->with('matiere')->get();
        
        if ($notes->isEmpty()) {
            return null;
        }
        
        $totalPoints = 0;
        $totalCoeffs = 0;
        
        foreach ($notes as $note) {
            $totalPoints += $note->note * $note->matiere->coefficient;
            $totalCoeffs += $note->matiere->coefficient;
        }
        
        return $totalCoeffs > 0 ? round($totalPoints / $totalCoeffs, 2) : null;
    }

    /**
     * Scope pour les élèves actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('status', 'actif');
    }

    /**
     * Scope pour les filles
     */
    public function scopeFilles($query)
    {
        return $query->where('sexe', 'F');
    }

    /**
     * Scope pour les garçons
     */
    public function scopeGarcons($query)
    {
        return $query->where('sexe', 'M');
    }

    /**
     * Scope pour les élèves avec impayés
     */
    public function scopeAvecImpayes($query)
    {
        return $query->whereDoesntHave('paiements', function($q) {
            $q->where('statut', 'paye');
        });
    }
}