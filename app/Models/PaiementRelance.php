<?php
// app/Models/PaiementRelance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PaiementRelance extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     */
    protected $table = 'paiement_relances';

    /**
     * Les attributs qui sont assignables en masse.
     */
    protected $fillable = [
        'paiement_id',
        'type',
        'statut',
        'message',
        'contact',
        'niveau_relance',
        'date_relance',
        'date_reponse',
        'notes',
        'cree_par'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     */
    protected $casts = [
        'date_relance' => 'date',
        'date_reponse' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Constantes pour les types de relance
     */
    const TYPE_EMAIL = 'email';
    const TYPE_SMS = 'sms';
    const TYPE_COURRIER = 'courrier';
    const TYPE_APPEL = 'appel';

    /**
     * Constantes pour les statuts de relance
     */
    const STATUT_ENVOYE = 'envoye';
    const STATUT_ECHEC = 'echec';
    const STATUT_EN_ATTENTE = 'en_attente';

    /**
     * Récupère le paiement associé
     */
    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    /**
     * Récupère l'utilisateur qui a créé la relance
     */
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    /**
     * Récupère le libellé du type de relance
     */
    public function getTypeLibelleAttribute(): string
    {
        return [
            self::TYPE_EMAIL => 'Email',
            self::TYPE_SMS => 'SMS',
            self::TYPE_COURRIER => 'Courrier',
            self::TYPE_APPEL => 'Appel téléphonique'
        ][$this->type] ?? $this->type;
    }

    /**
     * Récupère le libellé du statut
     */
    public function getStatutLibelleAttribute(): string
    {
        return [
            self::STATUT_ENVOYE => 'Envoyé',
            self::STATUT_ECHEC => 'Échec',
            self::STATUT_EN_ATTENTE => 'En attente'
        ][$this->statut] ?? $this->statut;
    }

    /**
     * Récupère la couleur du statut pour l'affichage
     */
    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            self::STATUT_ENVOYE => 'success',
            self::STATUT_ECHEC => 'danger',
            self::STATUT_EN_ATTENTE => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Vérifie si la relance peut être envoyée
     */
    public function peutEtreEnvoyee(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }

    /**
     * Marque la relance comme envoyée
     */
    public function marquerEnvoyee(): bool
    {
        return $this->update(['statut' => self::STATUT_ENVOYE]);
    }

    /**
     * Marque la relance comme échouée
     */
    public function marquerEchec(string $raison): bool
    {
        $notes = $this->notes 
            ? $this->notes . "\nÉchec: " . $raison 
            : "Échec: " . $raison;
        
        return $this->update([
            'statut' => self::STATUT_ECHEC,
            'notes' => $notes
        ]);
    }

    /**
     * Enregistre la réponse à la relance
     */
    public function reponse(string $message): bool
    {
        return $this->update([
            'date_reponse' => Carbon::now(),
            'notes' => $this->notes 
                ? $this->notes . "\nRéponse: " . $message 
                : "Réponse: " . $message
        ]);
    }
}