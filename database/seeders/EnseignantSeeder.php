<?php
// database/seeders/EnseignantSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enseignant;
use App\Models\User;
use App\Models\Matiere;
use App\Models\Etablissement;

class EnseignantSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🇨🇮 Création des enseignants ivoiriens...');
        
        $usersEnseignants = User::whereHas('role', function($q) {
            $q->where('name', 'enseignant');
        })->get();

        if ($usersEnseignants->isEmpty()) {
            $this->command->warn('Aucun utilisateur enseignant trouvé. Exécutez d\'abord UserSeeder.');
            return;
        }

        // Spécialités enseignées en Côte d'Ivoire
        $specialites = [
            'Mathématiques',
            'Physique-Chimie',
            'SVT',
            'Français',
            'Anglais',
            'Histoire-Géographie',
            'Philosophie',
            'EPS',
            'Informatique',
            'Arabe',
            'Espagnol',
            'Allemand',
            'Education Musicale',
            'Arts Plastiques',
            'Education Civique et Morale'
        ];

        // Opérateurs téléphoniques ivoiriens
        $operateurs = ['07', '05', '01', '02', '03', '04', '06'];

        // Villes de Côte d'Ivoire pour les adresses
        $villes = [
            'Abidjan', 'Bouaké', 'Daloa', 'Yamoussoukro', 'Korhogo', 'San-Pédro',
            'Gagnoa', 'Man', 'Divo', 'Anyama', 'Abengourou', 'Agboville'
        ];

        $quartiers = [
            'Cocody', 'Plateau', 'Yopougon', 'Marcory', 'Treichville', 'Adjamé',
            'Koumassi', 'Port-Bouët', 'Riviera', 'Deux-Plateaux', 'Angré'
        ];

        $totalEnseignants = 0;
        $enseignantsParSpecialite = array_fill_keys($specialites, 0);

        foreach ($usersEnseignants as $index => $user) {
            // Sélectionner une spécialité
            $specialite = $specialites[$index % count($specialites)];
            
            // Générer un numéro de téléphone ivoirien
            $telephone = $operateurs[array_rand($operateurs)] . rand(10000000, 99999999);
            
            // Générer une adresse
            $adresse = $quartiers[array_rand($quartiers)] . ', ' . $villes[array_rand($villes)];

            // Date d'embauche aléatoire entre 1990 et 2024
            $dateEmbauche = now()->subYears(rand(1, 30))->subMonths(rand(0, 11))->subDays(rand(0, 28));

            // Matricule selon le format ivoirien
            $matricule = 'ENS-' . date('Y') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            $enseignant = Enseignant::create([
                'user_id' => $user->id,
                'matricule' => $matricule,
                'specialite' => $specialite,
                'date_embauche' => $dateEmbauche,
                'telephone' => $telephone,
                'adresse' => $adresse,
            ]);

            // Associer des matières à l'enseignant (en fonction de sa spécialité)
            $this->associerMatieres($enseignant, $specialite);

            $enseignantsParSpecialite[$specialite]++;
            $totalEnseignants++;
        }

        // Statistiques finales
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 ENSEIGNANTS DE CÔTE D\'IVOIRE                ║');
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->info("Total enseignants: \033[32m{$totalEnseignants}\033[0m");
        
        // Répartition par spécialité
        $this->command->newLine();
        $this->command->info('RÉPARTITION PAR SPÉCIALITÉ :');
        
        $tableData = [];
        foreach ($enseignantsParSpecialite as $specialite => $count) {
            if ($count > 0) {
                $tableData[] = [$specialite, $count, round(($count / $totalEnseignants) * 100, 1) . '%'];
            }
        }
        
        $this->command->table(['Spécialité', 'Effectif', 'Pourcentage'], $tableData);

        // Exemples d'enseignants
        $this->command->newLine();
        $this->command->info('EXEMPLES D\'ENSEIGNANTS :');
        $exemples = Enseignant::with('user')->inRandomOrder()->take(5)->get();
        
        foreach ($exemples as $enseignant) {
            $anneesExperience = now()->diffInYears($enseignant->date_embauche);
            $this->command->line("   • {$enseignant->user->name} - {$enseignant->specialite}");
            $this->command->line("Matricule: {$enseignant->matricule}, {$enseignant->telephone}, {$anneesExperience} ans d'expérience");
        }

        $this->command->newLine();
        $this->command->info('Enseignants créés avec succès !');
    }

    /**
     * Associe des matières à l'enseignant en fonction de sa spécialité
     */
    private function associerMatieres($enseignant, $specialite): void
    {
        // Récupérer toutes les matières
        $matieres = Matiere::all();
        
        if ($matieres->isEmpty()) {
            return;
        }

        $matieresAAssocier = [];

        // Associer les matières selon la spécialité
        switch ($specialite) {
            case 'Mathématiques':
                $matieresAAssocier = $matieres->filter(function($m) {
                    return str_contains($m->nom, 'Math');
                })->pluck('id')->toArray();
                break;
                
            case 'Physique-Chimie':
                $matieresAAssocier = $matieres->filter(function($m) {
                    return str_contains($m->nom, 'Physique') || str_contains($m->nom, 'Chimie');
                })->pluck('id')->toArray();
                break;
                
            case 'SVT':
                $matieresAAssocier = $matieres->filter(function($m) {
                    return str_contains($m->nom, 'SVT') || str_contains($m->nom, 'Sciences');
                })->pluck('id')->toArray();
                break;
                
            case 'Français':
                $matieresAAssocier = $matieres->filter(function($m) {
                    return str_contains($m->nom, 'Franç') || str_contains($m->nom, 'Lecture') || str_contains($m->nom, 'Écriture');
                })->pluck('id')->toArray();
                break;
                
            case 'Anglais':
                $matieresAAssocier = $matieres->filter(function($m) {
                    return str_contains($m->nom, 'Anglais');
                })->pluck('id')->toArray();
                break;
                
            case 'Histoire-Géographie':
                $matieresAAssocier = $matieres->filter(function($m) {
                    return str_contains($m->nom, 'Histoire') || str_contains($m->nom, 'Géographie');
                })->pluck('id')->toArray();
                break;
                
            default:
                // Pour les autres spécialités, prendre la matière correspondante
                $matiere = $matieres->first(function($m) use ($specialite) {
                    return str_contains($m->nom, $specialite);
                });
                
                if ($matiere) {
                    $matieresAAssocier = [$matiere->id];
                }
        }

        // Ajouter quelques matières supplémentaires aléatoires
        if (!empty($matieresAAssocier)) {
            $enseignant->matieres()->sync($matieresAAssocier);
        } else {
            // Si aucune matière spécifique trouvée, prendre une matière au hasard
            $matiereRandom = $matieres->random();
            $enseignant->matieres()->sync([$matiereRandom->id]);
        }
    }

    /**
     * Génère un matricule selon le format du MENET (Ministère de l'Éducation)
     */
    private function genererMatriculeMENET($index): string
    {
        $annee = date('Y');
        $lettres = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = $lettres[rand(0, strlen($lettres) - 1)] . $lettres[rand(0, strlen($lettres) - 1)];
        
        return "MENET-{$annee}-{$code}" . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
    }
}