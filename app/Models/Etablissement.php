<?php
// app/Models/Etablissement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Etablissement extends Model
{
    protected $fillable = [
        'nom',
        'type',
        'adresse',
        'telephone',
        'email',
        'code_etablissement',
        'academie',
        'inspectorat',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec les utilisateurs de l'établissement
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relation avec les classes de l'établissement
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    /**
     * Relation avec les administrateurs de l'établissement
     */
    public function admins(): HasMany
    {
        return $this->hasMany(User::class)->whereHas('role', function($q) {
            $q->where('name', 'admin_etablissement');
        });
    }

    /**
     * Relation avec les enseignants de l'établissement
     */
    public function enseignants(): HasMany
    {
        return $this->hasMany(User::class)->whereHas('role', function($q) {
            $q->where('name', 'enseignant');
        });
    }

    /**
     * 👇 RELATION AJOUTÉE - Élèves de l'établissement (via les classes)
     * Permet d'utiliser $etablissement->eleves
     */
    public function eleves(): HasManyThrough
    {
        return $this->hasManyThrough(
            Eleve::class,      // Modèle cible
            Classe::class,     // Modèle intermédiaire
            'etablissement_id', // Clé étrangère sur classes
            'classe_id',        // Clé étrangère sur eleves
            'id',               // Clé locale sur etablissements
            'id'                // Clé locale sur classes
        );
    }

    /**
     * Comptage des élèves - Version optimisée pour withCount()
     */
    public function elevesCount(): int
    {
        return $this->eleves()->count();
    }

    /**
     * Accesseur pour le comptage (permet d'utiliser $etablissement->eleves_count)
     */
    public function getElevesCountAttribute(): int
    {
        return $this->eleves()->count();
    }

    /**
     * Statistiques rapides de l'établissement
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_classes' => $this->classes()->count(),
            'total_eleves' => $this->eleves()->count(),
            'total_enseignants' => $this->enseignants()->count(),
            'total_admins' => $this->admins()->count(),
        ];
    }
}