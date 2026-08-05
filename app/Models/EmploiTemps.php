<?php
// app/Models/EmploiTemps.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploiTemps extends Model
{
    protected $table = 'emplois_temps';
    
    protected $fillable = [
        'classe_id',
        'matiere_id',
        'enseignant_id',
        'jour',
        'heure_debut',
        'heure_fin',
        'salle',
    ];

    /**
     * Les attributs qui doivent être castés.
     * On ne caste pas heure_debut et heure_fin en datetime pour éviter les problèmes de format
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec la classe
     */
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec la matière
     */
    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    /**
     * Relation avec l'enseignant (User)
     */
    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }
    
    /**
     * Accesseur pour obtenir l'heure de début formatée (HH:MM)
     */
    public function getHeureDebutFormattedAttribute(): string
    {
        if (empty($this->heure_debut)) {
            return '';
        }
        // Si c'est déjà une chaîne, prendre les 5 premiers caractères
        if (is_string($this->heure_debut)) {
            return substr($this->heure_debut, 0, 5);
        }
        // Si c'est un objet Carbon
        if ($this->heure_debut instanceof \Carbon\Carbon) {
            return $this->heure_debut->format('H:i');
        }
        return '';
    }
    
    /**
     * Accesseur pour obtenir l'heure de fin formatée (HH:MM)
     */
    public function getHeureFinFormattedAttribute(): string
    {
        if (empty($this->heure_fin)) {
            return '';
        }
        // Si c'est déjà une chaîne, prendre les 5 premiers caractères
        if (is_string($this->heure_fin)) {
            return substr($this->heure_fin, 0, 5);
        }
        // Si c'est un objet Carbon
        if ($this->heure_fin instanceof \Carbon\Carbon) {
            return $this->heure_fin->format('H:i');
        }
        return '';
    }

    /**
     * Mutateur pour l'heure de début (nettoie le format)
     */
    public function setHeureDebutAttribute($value)
    {
        if ($value instanceof \Carbon\Carbon) {
            $this->attributes['heure_debut'] = $value->format('H:i');
        } elseif (is_string($value)) {
            // Ne garder que HH:MM
            $this->attributes['heure_debut'] = substr($value, 0, 5);
        } else {
            $this->attributes['heure_debut'] = $value;
        }
    }

    /**
     * Mutateur pour l'heure de fin (nettoie le format)
     */
    public function setHeureFinAttribute($value)
    {
        if ($value instanceof \Carbon\Carbon) {
            $this->attributes['heure_fin'] = $value->format('H:i');
        } elseif (is_string($value)) {
            // Ne garder que HH:MM
            $this->attributes['heure_fin'] = substr($value, 0, 5);
        } else {
            $this->attributes['heure_fin'] = $value;
        }
    }

    /**
     * Accesseur pour obtenir l'heure de début originale
     */
    public function getHeureDebutAttribute($value)
    {
        if ($value instanceof \Carbon\Carbon) {
            return $value->format('H:i');
        }
        return $value ? substr($value, 0, 5) : null;
    }

    /**
     * Accesseur pour obtenir l'heure de fin originale
     */
    public function getHeureFinAttribute($value)
    {
        if ($value instanceof \Carbon\Carbon) {
            return $value->format('H:i');
        }
        return $value ? substr($value, 0, 5) : null;
    }
}