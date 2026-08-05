<?php
// app/Services/EcheanceService.php

namespace App\Services;

use App\Models\Echeance;
use App\Models\Eleve;
use App\Models\FraisScolarite;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EcheanceService
{
    /**
     * Récupérer la situation financière d'un élève
     */
    public function getSituationFinanciere(Eleve $eleve): array
    {
        // Récupérer les échéances existantes
        $echeances = Echeance::where('eleve_id', $eleve->id)
            ->with('frais')
            ->orderBy('date_echeance')
            ->get();

        // Si des échéances existent, les utiliser
        if ($echeances->isNotEmpty()) {
            return $this->calculerSituationAvecEcheances($echeances, $eleve);
        }

        // Sinon, utiliser les données de l'élève (pas d'échéances)
        return $this->calculerSituationAvecDonneesEleve($eleve);
    }

    /**
     * Calculer la situation financière à partir des échéances
     */
    private function calculerSituationAvecEcheances($echeances, $eleve): array
    {
        $totalGeneral = $echeances->sum('montant');
        $totalPaye = $echeances->sum('montant_paye');
        $totalReste = $totalGeneral - $totalPaye;

        // Prochaines échéances (non payées)
        $prochainesEcheances = Echeance::where('eleve_id', $eleve->id)
            ->where('statut', '!=', 'paye')
            ->where('date_limite', '>=', Carbon::now())
            ->orderBy('date_limite')
            ->take(3)
            ->get();

        // Échéances en retard
        $echeancesRetard = Echeance::where('eleve_id', $eleve->id)
            ->where('statut', '!=', 'paye')
            ->where('date_limite', '<', Carbon::now())
            ->get();

        // Regrouper par type de frais
        $parFrais = [];
        foreach ($echeances as $e) {
            $type = $e->frais ? $e->frais->type : 'autre';
            if (!isset($parFrais[$type])) {
                $parFrais[$type] = [
                    'total' => 0,
                    'paye' => 0,
                    'reste' => 0,
                    'echeances' => []
                ];
            }
            $parFrais[$type]['total'] += $e->montant;
            $parFrais[$type]['paye'] += $e->montant_paye;
            $parFrais[$type]['reste'] += $e->montant_restant;
            $parFrais[$type]['echeances'][] = $e;
        }

        return [
            'total_general' => $totalGeneral,
            'total_paye' => $totalPaye,
            'total_reste' => $totalReste,
            'pourcentage_paye' => $totalGeneral > 0 ? round(($totalPaye / $totalGeneral) * 100, 2) : 0,
            'prochaines_echeances' => $prochainesEcheances,
            'echeances_retard' => $echeancesRetard,
            'toutes_echeances' => $echeances,
            'par_frais' => $parFrais,
            'source' => 'echeances'
        ];
    }

    /**
     * Calculer la situation financière à partir des données de l'élève
     */
    private function calculerSituationAvecDonneesEleve($eleve): array
    {
        // Récupérer tous les paiements de l'élève
        $paiements = Paiement::where('eleve_id', $eleve->id)->get();
        
        // Récupérer tous les frais de l'établissement
        $fraisGeneraux = FraisScolarite::where('etablissement_id', $eleve->classe->etablissement_id)
            ->where(function($q) use ($eleve) {
                $q->whereNull('classe_id')
                  ->orWhere('classe_id', $eleve->classe_id);
            })
            ->get();

        $totalGeneral = $eleve->montant_total_frais ?? 0;
        $totalPaye = $eleve->montant_paye ?? 0;
        $totalReste = $eleve->montant_restant ?? 0;

        // Si les totaux de l'élève sont vides, les calculer à partir des frais
        if ($totalGeneral == 0 && $fraisGeneraux->isNotEmpty()) {
            $totalGeneral = $fraisGeneraux->sum('montant');
            $totalPaye = $paiements->sum('montant');
            $totalReste = $totalGeneral - $totalPaye;
            
            // Mettre à jour les totaux de l'élève
            $eleve->update([
                'montant_total_frais' => $totalGeneral,
                'montant_paye' => $totalPaye,
                'montant_restant' => $totalReste
            ]);
        }

        // Regrouper les paiements par type de frais
        $parFrais = [];
        foreach ($paiements as $paiement) {
            $type = $paiement->frais ? $paiement->frais->type : 'autre';
            if (!isset($parFrais[$type])) {
                $parFrais[$type] = [
                    'total' => 0,
                    'paye' => 0,
                    'reste' => 0,
                    'echeances' => []
                ];
            }
            $parFrais[$type]['total'] += $paiement->montant;
            $parFrais[$type]['paye'] += $paiement->montant;
        }

        // Compléter avec les frais non payés
        foreach ($fraisGeneraux as $frais) {
            $type = $frais->type;
            if (!isset($parFrais[$type])) {
                $parFrais[$type] = [
                    'total' => $frais->montant,
                    'paye' => 0,
                    'reste' => $frais->montant,
                    'echeances' => []
                ];
            } elseif ($parFrais[$type]['total'] < $frais->montant) {
                $parFrais[$type]['total'] = $frais->montant;
                $parFrais[$type]['reste'] = $frais->montant - $parFrais[$type]['paye'];
            }
        }

        return [
            'total_general' => $totalGeneral,
            'total_paye' => $totalPaye,
            'total_reste' => $totalReste,
            'pourcentage_paye' => $totalGeneral > 0 ? round(($totalPaye / $totalGeneral) * 100, 2) : 0,
            'prochaines_echeances' => collect([]),
            'echeances_retard' => collect([]),
            'toutes_echeances' => collect([]),
            'par_frais' => $parFrais,
            'source' => 'eleve'
        ];
    }

    /**
     * Générer les échéances pour un élève et un frais
     */
    public function genererEcheances(Eleve $eleve, FraisScolarite $frais): array
    {
        $echeances = [];
        $dateDebut = $eleve->date_inscription ?? Carbon::now();
        $montantTotal = $frais->montant;
        
        switch ($frais->periodicite) {
            case 'mensuel':
                $nbMois = 9;
                $montantMensuel = round($montantTotal / $nbMois, 2);
                $date = Carbon::parse($dateDebut)->startOfMonth();
                
                for ($i = 1; $i <= $nbMois; $i++) {
                    $moisNom = $date->translatedFormat('F Y');
                    $echeances[] = [
                        'libelle' => $frais->libelle,
                        'periode' => $moisNom,
                        'montant' => $i == $nbMois ? $montantTotal - ($montantMensuel * ($nbMois - 1)) : $montantMensuel,
                        'date_echeance' => $date->copy(),
                        'date_limite' => $date->copy()->endOfMonth(),
                        'ordre' => $i
                    ];
                    $date->addMonth();
                }
                break;
                
            case 'trimestriel':
                $trimestres = [
                    1 => ['periode' => '1er Trimestre (Sept-Nov)', 'mois' => 3],
                    2 => ['periode' => '2ème Trimestre (Déc-Fév)', 'mois' => 6],
                    3 => ['periode' => '3ème Trimestre (Mar-Mai)', 'mois' => 9],
                ];
                $montantTrimestre = round($montantTotal / 3, 2);
                $date = Carbon::parse($dateDebut);
                
                foreach ($trimestres as $ordre => $trimestre) {
                    $echeances[] = [
                        'libelle' => $frais->libelle,
                        'periode' => $trimestre['periode'],
                        'montant' => $ordre == 3 ? $montantTotal - ($montantTrimestre * 2) : $montantTrimestre,
                        'date_echeance' => $date->copy()->addMonths($trimestre['mois'] - 3),
                        'date_limite' => $date->copy()->addMonths($trimestre['mois']),
                        'ordre' => $ordre
                    ];
                }
                break;
                
            case 'annuel':
                $echeances[] = [
                    'libelle' => $frais->libelle,
                    'periode' => 'Année scolaire',
                    'montant' => $montantTotal,
                    'date_echeance' => Carbon::parse($dateDebut),
                    'date_limite' => Carbon::parse($dateDebut)->addMonths(9),
                    'ordre' => 1
                ];
                break;
                
            case 'unique':
            default:
                $echeances[] = [
                    'libelle' => $frais->libelle,
                    'periode' => 'Unique',
                    'montant' => $montantTotal,
                    'date_echeance' => Carbon::parse($dateDebut),
                    'date_limite' => Carbon::parse($dateDebut)->addDays(30),
                    'ordre' => 1
                ];
                break;
        }
        
        return $echeances;
    }

    /**
     * Créer les échéances pour un élève
     */
    public function creerEcheancesPourEleve(Eleve $eleve): void
    {
        // Récupérer les frais obligatoires pour l'année en cours
        $frais = FraisScolarite::where('etablissement_id', $eleve->classe->etablissement_id)
            ->where(function($q) use ($eleve) {
                $q->whereNull('classe_id')
                  ->orWhere('classe_id', $eleve->classe_id);
            })
            ->get();
        
        foreach ($frais as $f) {
            $this->creerEcheancesPourFrais($eleve, $f);
        }
        
        // Mettre à jour les totaux de l'élève
        $this->mettreAJourTotauxEleve($eleve);
    }

    /**
     * Créer les échéances pour un frais spécifique
     */
    public function creerEcheancesPourFrais(Eleve $eleve, FraisScolarite $frais): void
    {
        // Supprimer les anciennes échéances
        Echeance::where('eleve_id', $eleve->id)
            ->where('frais_id', $frais->id)
            ->delete();
        
        $echeances = $this->genererEcheances($eleve, $frais);
        
        foreach ($echeances as $data) {
            Echeance::create([
                'eleve_id' => $eleve->id,
                'frais_id' => $frais->id,
                'libelle' => $data['libelle'],
                'periode' => $data['periode'],
                'montant' => $data['montant'],
                'date_echeance' => $data['date_echeance'],
                'date_limite' => $data['date_limite'],
                'ordre' => $data['ordre'],
                'statut' => 'en_attente'
            ]);
        }
    }

    /**
     * Mettre à jour les totaux d'un élève
     */
    public function mettreAJourTotauxEleve(Eleve $eleve): void
    {
        $echeances = Echeance::where('eleve_id', $eleve->id)->get();
        
        $totalFrais = $echeances->sum('montant');
        $totalPaye = $echeances->sum('montant_paye');
        
        $eleve->update([
            'montant_total_frais' => $totalFrais,
            'montant_paye' => $totalPaye,
            'montant_restant' => $totalFrais - $totalPaye
        ]);
    }

    /**
     * Enregistrer un paiement et mettre à jour les échéances
     */
    public function enregistrerPaiement(Eleve $eleve, float $montant, array $options): array
    {
        DB::beginTransaction();
        
        try {
            // Récupérer les échéances non payées
            $echeances = Echeance::where('eleve_id', $eleve->id)
                ->where('statut', '!=', 'paye')
                ->orderBy('ordre')
                ->orderBy('date_limite')
                ->get();
            
            $montantRestant = $montant;
            $echeancesModifiees = [];
            
            foreach ($echeances as $echeance) {
                if ($montantRestant <= 0) break;
                
                $resteEcheance = $echeance->montant - $echeance->montant_paye;
                $aPayer = min($montantRestant, $resteEcheance);
                
                $echeance->montant_paye += $aPayer;
                $echeance->statut = $echeance->montant_paye >= $echeance->montant ? 'paye' : 'partiel';
                $echeance->save();
                
                $montantRestant -= $aPayer;
                $echeancesModifiees[] = [
                    'echeance' => $echeance,
                    'paye' => $aPayer
                ];
            }
            
            // Créer le paiement
            $reference = $options['reference'] ?? 'PAY-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $paiement = Paiement::create([
                'eleve_id' => $eleve->id,
                'frais_id' => $options['frais_id'] ?? null,
                'montant' => $montant,
                'montant_paye' => $montant,
                'montant_restant' => $montantRestant,
                'date_paiement' => $options['date_paiement'] ?? now(),
                'date_echeance' => now()->addDays(30),
                'date_limite' => $options['date_limite'] ?? now()->addDays(30),
                'mode_paiement' => $options['mode_paiement'] ?? 'especes',
                'reference' => $reference,
                'statut' => $montantRestant > 0 ? 'partiel' : 'paye',
                'commentaire' => $options['commentaire'] ?? null,
                'cree_par' => auth()->id()
            ]);
            
            // Lier les échéances au paiement
            foreach ($echeancesModifiees as $item) {
                $item['echeance']->update(['paiement_id' => $paiement->id]);
            }
            
            // Mettre à jour les totaux de l'élève
            $this->mettreAJourTotauxEleve($eleve);
            
            DB::commit();
            
            return [
                'success' => true,
                'paiement' => $paiement,
                'echeances' => $echeancesModifiees,
                'montant_restant_global' => $montantRestant
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}