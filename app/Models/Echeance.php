<?php
// app/Models/Echeance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Echeance extends Model
{
    protected $table = 'echeances';
    
    protected $fillable = [
        'eleve_id',
        'frais_id',
        'paiement_id',
        'libelle',
        'description',
        'montant',
        'montant_paye',
        'date_echeance',
        'date_limite',
        'statut',
        'ordre',
        'periode',
        'notes'
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'date_limite' => 'date',
        'montant' => 'decimal:2',
        'montant_paye' => 'decimal:2'
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function frais(): BelongsTo
    {
        return $this->belongsTo(FraisScolarite::class);
    }

    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    public function getMontantRestantAttribute(): float
    {
        return $this->montant - $this->montant_paye;
    }

    public function getEstPayeAttribute(): bool
    {
        return $this->montant_paye >= $this->montant;
    }

    public function getEstEnRetardAttribute(): bool
    {
        return !$this->est_paye && $this->date_limite && $this->date_limite < Carbon::now();
    }

    public function getPourcentagePayeAttribute(): float
    {
        if ($this->montant <= 0) return 0;
        return round(($this->montant_paye / $this->montant) * 100, 2);
    }
}