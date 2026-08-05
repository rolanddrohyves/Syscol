<?php
// database/seeders/ClasseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;
use App\Models\Etablissement;
use App\Models\AnneeScolaire; 

class ClasseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🇨🇮 Création des classes pour les établissements ivoiriens...');
        
        $etablissements = Etablissement::all();
        
        // Récupérer l'année scolaire en cours
        $anneeScolaire = AnneeScolaire::where('is_current', true)->first();
        
        if (!$anneeScolaire) {
            $anneeScolaire = AnneeScolaire::first();
        }
        
        if (!$anneeScolaire) {
            $anneeScolaire = AnneeScolaire::create([
                'libelle' => '2024-2025',
                'date_debut' => '2024-09-15',
                'date_fin' => '2025-07-15',
                'is_current' => true,
            ]);
        }
        
        // Système éducatif ivoirien
        $niveaux = [
            'Primaire' => [
                'CI' => 'Cours d\'Initiation',
                'CP' => 'Cours Préparatoire',
                'CE1' => 'Cours Élémentaire 1',
                'CE2' => 'Cours Élémentaire 2',
                'CM1' => 'Cours Moyen 1',
                'CM2' => 'Cours Moyen 2'
            ],
            'Collège' => [
                '6ème' => 'Sixième',
                '5ème' => 'Cinquième',
                '4ème' => 'Quatrième',
                '3ème' => 'Troisième'
            ],
            'Lycée' => [
                'Seconde' => 'Seconde',
                'Première' => 'Première',
                'Terminale' => 'Terminale'
            ],
        ];

        // Séries du bac ivoirien
        $series = [
            'A' => 'Littéraire',
            'C' => 'Mathématiques et Sciences physiques',
            'D' => 'Sciences biologiques',
            'E' => 'Mathématiques et Techniques',
            'F' => 'Industrielle',
            'G' => 'Commerciale et comptable',
            'H' => 'Artistique'
        ];

        // Séries par niveau
        $seriesParNiveau = [
            'Seconde' => ['A', 'C', 'D'], // Seconde indifférenciée
            'Première' => ['A', 'C', 'D', 'E', 'F', 'G'],
            'Terminale' => ['A', 'C', 'D', 'E', 'F', 'G'],
        ];

        $totalClasses = 0;

        foreach ($etablissements as $etablissement) {
            $classes = [];
            $compteur = 0;

            if ($etablissement->type === 'Primaire') {
                // École primaire - classes de base
                foreach ($niveaux['Primaire'] as $code => $libelle) {
                    // 2 classes par niveau (A et B)
                    for ($i = 1; $i <= 2; $i++) {
                        $suffixe = $i == 1 ? 'A' : 'B';
                        $classes[] = [
                            'etablissement_id' => $etablissement->id,
                            'nom' => "{$code} {$suffixe}",
                            'niveau' => 'Primaire',
                            'capacite' => 30,
                            'annee_scolaire_id' => $anneeScolaire->id,
                        ];
                        $compteur++;
                    }
                }
            } 
            elseif ($etablissement->type === 'Collège') {
                // Collège - classes de la 6ème à la 3ème
                foreach ($niveaux['Collège'] as $code => $libelle) {
                    // 3 classes par niveau (A, B, C)
                    for ($i = 1; $i <= 3; $i++) {
                        $suffixe = chr(64 + $i); // A, B, C
                        $classes[] = [
                            'etablissement_id' => $etablissement->id,
                            'nom' => "{$code} {$suffixe}",
                            'niveau' => 'Collège',
                            'capacite' => 35,
                            'annee_scolaire_id' => $anneeScolaire->id,
                        ];
                        $compteur++;
                    }
                }
            } 
            elseif ($etablissement->type === 'Lycée') {
                // Lycée - classes avec séries
                foreach ($niveaux['Lycée'] as $code => $libelle) {
                    $seriesNiveau = $seriesParNiveau[$code] ?? ['A', 'C', 'D'];
                    
                    foreach ($seriesNiveau as $serie) {
                        // 2 classes par série (A et B)
                        for ($i = 1; $i <= 2; $i++) {
                            $suffixe = $i == 1 ? 'A' : 'B';
                            $nom = $code == 'Seconde' 
                                ? "{$code} {$serie}" 
                                : "{$code} {$serie}{$suffixe}";
                            
                            $classes[] = [
                                'etablissement_id' => $etablissement->id,
                                'nom' => $nom,
                                'niveau' => 'Lycée',
                                'serie' => $serie,
                                'capacite' => 30,
                                'annee_scolaire_id' => $anneeScolaire->id,
                            ];
                            $compteur++;
                        }
                    }
                }
            } 
            elseif ($etablissement->type === 'Primaire/Secondaire') {
                // Établissement complet (primaire + secondaire)
                
                // Primaire
                foreach ($niveaux['Primaire'] as $code => $libelle) {
                    for ($i = 1; $i <= 2; $i++) {
                        $suffixe = $i == 1 ? 'A' : 'B';
                        $classes[] = [
                            'etablissement_id' => $etablissement->id,
                            'nom' => "{$code} {$suffixe}",
                            'niveau' => 'Primaire',
                            'capacite' => 30,
                            'annee_scolaire_id' => $anneeScolaire->id,
                        ];
                        $compteur++;
                    }
                }
                
                // Collège
                foreach ($niveaux['Collège'] as $code => $libelle) {
                    for ($i = 1; $i <= 3; $i++) {
                        $suffixe = chr(64 + $i);
                        $classes[] = [
                            'etablissement_id' => $etablissement->id,
                            'nom' => "{$code} {$suffixe}",
                            'niveau' => 'Collège',
                            'capacite' => 35,
                            'annee_scolaire_id' => $anneeScolaire->id,
                        ];
                        $compteur++;
                    }
                }
                
                // Lycée (séries principales)
                foreach ($niveaux['Lycée'] as $code => $libelle) {
                    $seriesNiveau = $code == 'Seconde' ? ['A', 'C', 'D'] : ['A', 'C', 'D'];
                    
                    foreach ($seriesNiveau as $serie) {
                        for ($i = 1; $i <= 2; $i++) {
                            $suffixe = $i == 1 ? 'A' : 'B';
                            $nom = $code == 'Seconde' 
                                ? "{$code} {$serie}" 
                                : "{$code} {$serie}{$suffixe}";
                            
                            $classes[] = [
                                'etablissement_id' => $etablissement->id,
                                'nom' => $nom,
                                'niveau' => 'Lycée',
                                'serie' => $serie,
                                'capacite' => 30,
                                'annee_scolaire_id' => $anneeScolaire->id,
                            ];
                            $compteur++;
                        }
                    }
                }
            }

            // Insertion des classes
            foreach ($classes as $classe) {
                Classe::create($classe);
            }
            
            $totalClasses += $compteur;
            $this->command->info("   ➜ {$etablissement->nom}: \033[32m{$compteur}\033[0m classes créées");
        }

        // Statistiques finales
        $this->command->newLine();
        $this->command->info('╔════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 CLASSES DU SYSTÈME ÉDUCATIF IVOIRIEN    ║');
        $this->command->info('╚════════════════════════════════════════════════╝');
        $this->command->info("Année scolaire: \033[33m{$anneeScolaire->libelle}\033[0m");
        $this->command->info("Établissements: \033[32m" . $etablissements->count() . "\033[0m");
        $this->command->info("Total classes: \033[32m{$totalClasses}\033[0m");
        
        // Répartition par niveau
        $this->command->newLine();
        $this->command->info('RÉPARTITION PAR NIVEAU :');
        $this->command->table(
            ['Niveau', 'Nombre de classes'],
            [
                ['Primaire', Classe::where('niveau', 'Primaire')->count()],
                ['Collège', Classe::where('niveau', 'Collège')->count()],
                ['Lycée', Classe::where('niveau', 'Lycée')->count()],
            ]
        );
        
        $this->command->info('Classes créées avec succès !');
    }
}