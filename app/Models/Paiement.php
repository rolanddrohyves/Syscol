<?php
// app/Models/Paiement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $table = 'paiements';
    
    protected $fillable = [
        'eleve_id',
        'frais_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference',
        'statut',
        'commentaire',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
    ];

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function frais(): BelongsTo
    {
        return $this->belongsTo(FraisScolarite::class, 'frais_id');
    }
    
    public function relances()
    {
        return $this->hasMany(PaiementRelance::class);
    }

    public function engagements()
    {
        return $this->hasMany(EngagementPaiement::class);
    }

    public function scopeImpayes($query, $dateDebut = null, $dateFin = null)
    {
        $query->where('statut', 'en_attente')
            ->orWhere('statut', 'partiel');
        
        if ($dateDebut && $dateFin) {
            $query->whereBetween('date_echeance', [$dateDebut, $dateFin]);
        }
        
        return $query;
    }

    public function scopeEnRetard($query)
    {
        return $query->whereIn('statut', ['en_attente', 'partiel'])
                    ->where('date_echeance', '<', now());
    }
}