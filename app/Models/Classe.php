<?php
// app/Models/Classe.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // 👈 AJOUTÉ

class Classe extends Model
{
    protected $fillable = [
        'etablissement_id',
        'nom',
        'niveau',
        'serie',
        'capacite',
        'annee_scolaire_id',
        'professeur_principal_id',
    ];

    protected $casts = [
        'capacite' => 'integer',
    ];

    // ============================================
    // RELATIONS EXISTANTES
    // ============================================
    
    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function professeurPrincipal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professeur_principal_id');
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class);
    }

    public function emploisTemps(): HasMany
    {
        return $this->hasMany(EmploiTemps::class);
    }

    // ============================================
    // NOUVELLES RELATIONS
    // ============================================

    /**
     * ✅ Enseignants qui enseignent dans cette classe (via table pivot)
     */
    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'enseignant_classe',
            'classe_id',
            'enseignant_user_id'
        )->withTimestamps();
    }

    /**
     * ✅ Matières enseignées dans cette classe (via emplois du temps)
     */
    public function matieres()
    {
        return $this->belongsToMany(
            Matiere::class,
            'emplois_temps',
            'classe_id',
            'matiere_id'
        )->distinct();
    }

    // ============================================
    // ATTRIBUTS CALCULÉS
    // ============================================

    public function getEffectifAttribute(): int
    {
        return $this->eleves()->count();
    }

    public function getPlacesDisponiblesAttribute(): int
    {
        return $this->capacite - $this->effectif;
    }

    /**
     * ✅ Taux d'occupation de la classe
     */
    public function getTauxOccupationAttribute(): float
    {
        return $this->capacite > 0 
            ? round(($this->effectif / $this->capacite) * 100, 1) 
            : 0;
    }

    /**
     * ✅ Nom complet de la classe (avec série si applicable)
     */
    public function getNomCompletAttribute(): string
    {
        return $this->serie 
            ? "{$this->nom} {$this->serie}" 
            : $this->nom;
    }

    /**
     * ✅ Liste des enseignants avec leurs matières
     */
    public function getEnseignantsAvecMatieresAttribute()
    {
        return $this->emploisTemps()
            ->with(['enseignant', 'matiere'])
            ->get()
            ->groupBy('enseignant_id')
            ->map(function($cours) {
                $enseignant = $cours->first()->enseignant;
                $matieres = $cours->pluck('matiere')->unique('id');
                return [
                    'enseignant' => $enseignant,
                    'matieres' => $matieres
                ];
            });
    }

    // ============================================
    // SCOPES
    // ============================================

    /**
     * Scope pour filtrer par niveau
     */
    public function scopeNiveau($query, $niveau)
    {
        return $query->where('niveau', $niveau);
    }

    /**
     * Scope pour les classes avec professeur principal
     */
    public function scopeAvecProfesseurPrincipal($query)
    {
        return $query->whereNotNull('professeur_principal_id');
    }

    /**
     * Scope pour les classes sans professeur principal
     */
    public function scopeSansProfesseurPrincipal($query)
    {
        return $query->whereNull('professeur_principal_id');
    }

    /**
     * Scope pour les classes disponibles (places libres)
     */
    public function scopeDisponibles($query)
    {
        return $query->whereColumn('eleves_count', '<', 'capacite');
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    /**
     * Vérifie si la classe a des places disponibles
     */
    public function hasPlacesDisponibles(int $nombre = 1): bool
    {
        return $this->places_disponibles >= $nombre;
    }

    /**
     * Vérifie si un enseignant enseigne dans cette classe
     */
    public function hasEnseignant($enseignantId): bool
    {
        return $this->emploisTemps()
            ->where('enseignant_id', $enseignantId)
            ->exists();
    }

    /**
     * Récupère l'emploi du temps groupé par jour
     */
    public function getEmploiTempsParJour()
    {
        return $this->emploisTemps()
            ->with(['matiere', 'enseignant'])
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('jour');
    }

    /**
     * Statistiques de la classe
     */
    public function getStatsAttribute(): array
    {
        return [
            'effectif' => $this->effectif,
            'places_disponibles' => $this->places_disponibles,
            'taux_occupation' => $this->taux_occupation,
            'nb_filles' => $this->eleves()->where('sexe', 'F')->count(),
            'nb_garcons' => $this->eleves()->where('sexe', 'M')->count(),
            'nb_enseignants' => $this->enseignants()->count(),
            'nb_matieres' => $this->matieres()->count(),
        ];
    }
}