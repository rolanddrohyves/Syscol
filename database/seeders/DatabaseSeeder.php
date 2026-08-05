<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('╔════════════════════════════════════════════════════╗');
        $this->command->info('║   SYSCOL CI - SYSTÈME SCOLAIRE IVOIRIEN       ║');
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->newLine();

        // ORDRE CORRECT pour la base de données ivoirienne
        $seeders = [
            // ============================================
            // NIVEAU 1 - Tables indépendantes
            // ============================================
            [
                'name' => 'Rôles et permissions',
                'class' => RoleSeeder::class,
                'description' => 'Création des rôles (Admin, Directeur, Enseignant, Parent, Élève...)'
            ],
            [
                'name' => 'Établissements scolaires',
                'class' => EtablissementSeeder::class,
                'description' => 'Établissements publics et privés de Côte d\'Ivoire'
            ],
            
            // ============================================
            // NIVEAU 2 - Tables dépendantes simples
            // ============================================
            [
                'name' => 'Années scolaires',
                'class' => AnneeScolaireSeeder::class,
                'description' => 'Calendrier scolaire ivoirien (2023-2026)'
            ],
            [
                'name' => 'Matières enseignées',
                'class' => MatiereSeeder::class,
                'description' => 'Programmes éducatifs ivoiriens'
            ],
            
            // ============================================
            // NIVEAU 3 - Utilisateurs
            // ============================================
            [
                'name' => 'Utilisateurs standards',
                'class' => UserSeeder::class,
                'description' => 'Enseignants, parents et élèves'
            ],
            [
                'name' => 'Administrateurs',
                'class' => AdminUserSeeder::class,
                'description' => 'Super Admin, DRENET, Chefs d\'établissement'
            ],
            
            // ============================================
            // NIVEAU 4 - Structures pédagogiques
            // ============================================
            [
                'name' => 'Classes',
                'class' => ClasseSeeder::class,
                'description' => 'Classes du système éducatif ivoirien (primaire au lycée)'
            ],
            [
                'name' => 'Enseignants (profils)',
                'class' => EnseignantSeeder::class,
                'description' => 'Profils détaillés des enseignants'
            ],
            [
                'name' => 'Élèves',
                'class' => EleveSeeder::class,
                'description' => 'Élèves inscrits avec leurs informations'
            ],
            
            // ============================================
            // NIVEAU 5 - Données complémentaires (optionnel)
            // ============================================
            [
                'name' => 'Configuration système',
                'class' => ConfigurationSeeder::class,
                'description' => 'Paramètres généraux du système'
            ],
        ];

        $totalSeeders = count($seeders);
        $successCount = 0;

        foreach ($seeders as $index => $seeder) {
            $progress = "[" . ($index + 1) . "/{$totalSeeders}]";
            
            $this->command->info("{$progress} \033[33m{$seeder['name']}...\033[0m");
            $this->command->line("     └─ {$seeder['description']}");
            
            try {
                $this->call($seeder['class']);
                $successCount++;
                $this->command->info("{$progress} \033[32mTerminé avec succès !\033[0m");
            } catch (\Exception $e) {
                $this->command->error("{$progress}  Erreur : " . $e->getMessage());
                $this->command->warn("     Arrêt du seeding à cause de l'erreur.");
                break;
            }
            
            $this->command->newLine();
        }

        // Résumé final
        $this->command->newLine(2);
        $this->command->info('╔════════════════════════════════════════════════════╗');
        
        if ($successCount === $totalSeeders) {
            $this->command->info('║   SYSCOL CI - INITIALISATION RÉUSSIE !       ║');
            $this->command->info('║   Tous les seeders ont été exécutés           ║');
        } else {
            $this->command->warn('║   SYSCOL CI - INITIALISATION PARTIELLE       ║');
            $this->command->warn("║      {$successCount}/{$totalSeeders} seeders réussis        ║");
        }
        
        $this->command->info('╚════════════════════════════════════════════════════╝');
        $this->command->newLine();

        // Afficher les identifiants de test
        $this->afficherIdentifiants();
    }

    /**
     * Affiche les identifiants de test pour le contexte ivoirien
     */
    private function afficherIdentifiants(): void
    {
        $this->command->newLine();
        $this->command->info('IDENTIFIANTS DE TEST - CÔTE D\'IVOIRE');
        $this->command->info('═══════════════════════════════════════════════════');
        
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['Super Admin MENA', 'super.admin@syscol.ci', 'Super@2024!Secure'],
                ['Admin Abidjan', 'admin.abidjan@syscol.ci', 'Admin@2024!'],
                ['Admin Bouaké', 'admin.bouake@syscol.ci', 'Admin@2024!'],
                ['Directeur Études', 'directeur.abidjan@syscol.ci', 'Directeur@2024!'],
                ['CPE', 'cpe.abidjan@syscol.ci', 'CPE@2024!'],
                ['Comptable', 'comptable.abidjan@syscol.ci', 'Compta@2024!'],
                ['Enseignant (Maths)', 'kouadio.paul@ecole.ci', 'Enseignant@2024!'],
                ['Enseignant (Français)', 'konan.marie@ecole.ci', 'Enseignant@2024!'],
                ['Parent', 'parent.traore@ecole.ci', 'Parent@2024!'],
                ['Élève', 'eleve.kouadio@ecole.ci', 'Eleve@2024!'],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('INFORMATIONS COMPLÉMENTAIRES :');
        $this->command->info('   • Fuseau horaire : Africa/Abidjan (GMT)');
        $this->command->info('   • Monnaie : Franc CFA (FCFA)');
        $this->command->info('   • Format téléphone : +225 ## ## ## ##');
        $this->command->info('   • Système éducatif : 6-3-3 (primaire, collège, lycée)');
        
        $this->command->newLine();
        $this->command->info('Vous pouvez maintenant vous connecter avec ces identifiants.');
        $this->command->info('Accédez à : http://localhost:8000/login');
    }
}