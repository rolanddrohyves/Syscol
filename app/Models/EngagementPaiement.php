<?php
// app/Models/EngagementPaiement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EngagementPaiement extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     */
    protected $table = 'engagements_paiement';

    /**
     * Les attributs qui sont assignables en masse.
     */
    protected $fillable = [
        'eleve_id',
        'paiement_id',
        'montant_total',
        'montant_paye',
        'date_engagement',
        'date_echeance',
        'statut',
        'frequence',
        'conditions',
        'cree_par'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     */
    protected $casts = [
        'date_engagement' => 'date',
        'date_echeance' => 'date',
        'montant_total' => 'decimal:3',
        'montant_paye' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Constantes pour les statuts
     */
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_RESPECTE = 'respecte';
    const STATUT_NON_RESPECTE = 'non_respecte';
    const STATUT_RENEGOCIE = 'renégocié';

    /**
     * Constantes pour les fréquences
     */
    const FREQUENCE_UNIQUE = 'unique';
    const FREQUENCE_HEBDOMADAIRE = 'hebdomadaire';
    const FREQUENCE_MENSUEL = 'mensuel';

    /**
     * Récupère l'élève
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    /**
     * Récupère le paiement associé
     */
    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    /**
     * Récupère l'utilisateur qui a créé l'engagement
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    /**
     * Récupère le montant restant dû
     */
    public function getMontantRestantAttribute(): float
    {
        return $this->montant_total - $this->montant_paye;
    }

    /**
     * Vérifie si l'engagement est en retard
     */
    public function getEstEnRetardAttribute(): bool
    {
        return $this->statut === self::STATUT_EN_COURS 
            && $this->date_echeance 
            && $this->date_echeance < Carbon::now()
            && $this->montant_restant > 0;
    }

    /**
     * Vérifie si l'engagement est respecté
     */
    public function getEstRespecteAttribute(): bool
    {
        return $this->statut === self::STATUT_RESPECTE 
            || ($this->montant_paye >= $this->montant_total);
    }

    /**
     * Récupère le pourcentage payé
     */
    public function getPourcentagePayeAttribute(): float
    {
        if ($this->montant_total <= 0) {
            return 0;
        }
        return round(($this->montant_paye / $this->montant_total) * 100, 2);
    }

    /**
     * Récupère le libellé du statut
     */
    public function getStatutLibelleAttribute(): string
    {
        return [
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_RESPECTE => 'Respecté',
            self::STATUT_NON_RESPECTE => 'Non respecté',
            self::STATUT_RENEGOCIE => 'Renégocié'
        ][$this->statut] ?? $this->statut;
    }

    /**
     * Récupère le libellé de la fréquence
     */
    public function getFrequenceLibelleAttribute(): string
    {
        return [
            self::FREQUENCE_UNIQUE => 'Paiement unique',
            self::FREQUENCE_HEBDOMADAIRE => 'Hebdomadaire',
            self::FREQUENCE_MENSUEL => 'Mensuel'
        ][$this->frequence] ?? $this->frequence;
    }

    /**
     * Marque l'engagement comme respecté
     */
    public function marquerRespecte(): bool
    {
        return $this->update(['statut' => self::STATUT_RESPECTE]);
    }

    /**
     * Marque l'engagement comme non respecté
     */
    public function marquerNonRespecte(): bool
    {
        return $this->update(['statut' => self::STATUT_NON_RESPECTE]);
    }

    /**
     * Renégocie l'engagement
     */
    public function renegocier(float $nouveauMontant, ?string $nouvellesConditions = null): bool
    {
        return $this->update([
            'montant_total' => $nouveauMontant,
            'conditions' => $nouvellesConditions ?? $this->conditions,
            'statut' => self::STATUT_RENEGOCIE
        ]);
    }

    /**
     * Ajoute un paiement à l'engagement
     */
    public function ajouterPaiement(float $montant): bool
    {
        $nouveauPaye = $this->montant_paye + $montant;
        $nouveauStatut = $nouveauPaye >= $this->montant_total 
            ? self::STATUT_RESPECTE 
            : self::STATUT_EN_COURS;
        
        return $this->update([
            'montant_paye' => $nouveauPaye,
            'statut' => $nouveauStatut
        ]);
    }
}