<?php
// app/Models/Depense.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    protected $table = 'depenses';
    
    protected $fillable = [
        'etablissement_id',
        'libelle',
        'description',
        'montant',
        'date',
        'categorie',
        'mode_paiement',
        'beneficiaire',
        'piece_jointe',
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }
}