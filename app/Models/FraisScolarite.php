<?php
// app/Models/FraisScolarite.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FraisScolarite extends Model
{
    protected $table = 'frais_scolarites';
    
    protected $fillable = [
        'etablissement_id',
        'annee_scolaire_id',
        'classe_id',
        'libelle',
        'description',
        'montant',
        'type',
        'periodicite',
        'obligatoire'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'obligatoire' => 'boolean'
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    // CORRECTION : Spécifier la clé étrangère 'frais_id'
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'frais_id');
    }

    // Ajouter la relation avec les échéances
    public function echeances()
    {
        return $this->hasMany(EcheanceFrais::class, 'frais_id');
    }

    // Accessoire pour le libellé du type
    public function getTypeLabelAttribute()
    {
        $types = [
            'inscription' => 'Inscription',
            'scolarite' => 'Scolarité',
            'cantine' => 'Cantine',
            'transport' => 'Transport',
            'sortie' => 'Sortie pédagogique',
            'autre' => 'Autre'
        ];
        return $types[$this->type] ?? ucfirst($this->type);
    }

    // Accessoire pour le libellé de la périodicité
    public function getPeriodiciteLabelAttribute()
    {
        $periodicites = [
            'unique' => 'Paiement unique',
            'mensuel' => 'Mensuel',
            'trimestriel' => 'Trimestriel',
            'annuel' => 'Annuel'
        ];
        return $periodicites[$this->periodicite] ?? ucfirst($this->periodicite);
    }
}