<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Etablissement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Désactiver temporairement les logs d'activité
        $this->disableActivityLogging();
        
        try {
            $this->command->info('🇨🇮 Création des administrateurs pour la Côte d\'Ivoire...');
            
            $roles = Role::all()->keyBy('name');
            $etablissements = Etablissement::all();
            
            // Vérifications préalables
            if ($roles->isEmpty()) {
                $this->command->error('Aucun rôle trouvé !');
                return;
            }

            // ============================================
            // SUPER ADMIN - Administration générale
            // ============================================
            User::updateOrCreate(
                ['email' => 'super.admin@syscol.ci'],
                [
                    'name' => 'Administrateur Général MENET',
                    'password' => Hash::make('Super@2024!Secure'),
                    'email_verified_at' => now(),
                    'role_id' => $roles['super_admin']->id ?? 1,
                    'etablissement_id' => null,
                ]
            );
            $this->command->info('✓ Super Admin MENET créé (super.admin@syscol.ci)');

            // ============================================
            // ADMIN ÉTABLISSEMENT - Par région académique
            // ============================================
            if ($etablissements->isNotEmpty()) {
                // Abidjan
                User::updateOrCreate(
                    ['email' => 'admin.abidjan@syscol.ci'],
                    [
                        'name' => 'Admin Abidjan - DRENET',
                        'password' => Hash::make('Admin@2024!'),
                        'email_verified_at' => now(),
                        'role_id' => $roles['admin_etablissement']->id ?? 2,
                        'etablissement_id' => $etablissements->first()->id,
                    ]
                );
                $this->command->info('✓ Admin Abidjan créé (admin.abidjan@syscol.ci)');

                // Bouaké
                if ($etablissements->count() > 1) {
                    User::updateOrCreate(
                        ['email' => 'admin.bouake@syscol.ci'],
                        [
                            'name' => 'Admin Bouaké - DRENET',
                            'password' => Hash::make('Admin@2024!'),
                            'email_verified_at' => now(),
                            'role_id' => $roles['admin_etablissement']->id ?? 2,
                            'etablissement_id' => $etablissements->skip(1)->first()->id,
                        ]
                    );
                    $this->command->info('✓ Admin Bouaké créé (admin.bouake@syscol.ci)');
                }
            } else {
                $this->command->warn('⚠ Admin Établissement non créé (aucun établissement)');
            }

            // ============================================
            // DIRECTEURS DES ÉTUDES
            // ============================================
            if ($etablissements->isNotEmpty()) {
                // Directeur Études Abidjan
                User::updateOrCreate(
                    ['email' => 'directeur.abidjan@syscol.ci'],
                    [
                        'name' => 'Koné Ibrahim - Directeur des Études',
                        'password' => Hash::make('Directeur@2024!'),
                        'email_verified_at' => now(),
                        'role_id' => $roles['directeur_etudes']->id ?? 3,
                        'etablissement_id' => $etablissements->first()->id,
                    ]
                );
                $this->command->info('✓ Directeur Études Abidjan créé');

                // Directeur Études Bouaké
                if ($etablissements->count() > 1) {
                    User::updateOrCreate(
                        ['email' => 'directeur.bouake@syscol.ci'],
                        [
                            'name' => 'Touré Fatoumata - Directrice des Études',
                            'password' => Hash::make('Directeur@2024!'),
                            'email_verified_at' => now(),
                            'role_id' => $roles['directeur_etudes']->id ?? 3,
                            'etablissement_id' => $etablissements->skip(1)->first()->id,
                        ]
                    );
                    $this->command->info('✓ Directrice Études Bouaké créée');
                }
            }

            // ============================================
            // CPE (Conseillers Principaux d'Éducation)
            // ============================================
            if ($etablissements->isNotEmpty()) {
                User::updateOrCreate(
                    ['email' => 'cpe.abidjan@syscol.ci'],
                    [
                        'name' => 'N\'Guessan Antoine - CPE',
                        'password' => Hash::make('CPE@2024!'),
                        'email_verified_at' => now(),
                        'role_id' => $roles['cpe']->id ?? 4,
                        'etablissement_id' => $etablissements->first()->id,
                    ]
                );
                $this->command->info('✓ CPE Abidjan créé');
            }

            // ============================================
            // COMPTABLES
            // ============================================
            if ($etablissements->isNotEmpty()) {
                User::updateOrCreate(
                    ['email' => 'comptable.abidjan@syscol.ci'],
                    [
                        'name' => 'Bamba Moussa - Agent Comptable',
                        'password' => Hash::make('Compta@2024!'),
                        'email_verified_at' => now(),
                        'role_id' => $roles['comptable']->id ?? 5,
                        'etablissement_id' => $etablissements->first()->id,
                    ]
                );
                $this->command->info('✓ Comptable Abidjan créé');
            }

            // ============================================
            // ENSEIGNANTS
            // ============================================
            if ($etablissements->isNotEmpty()) {
                $enseignants = [
                    [
                        'email' => 'kouadio.paul@ecole.ci',
                        'name' => 'Kouadio Paul',
                        'specialite' => 'Mathématiques'
                    ],
                    [
                        'email' => 'konan.marie@ecole.ci',
                        'name' => 'Konan Marie',
                        'specialite' => 'Français'
                    ],
                    [
                        'email' => 'koffi.jean@ecole.ci',
                        'name' => 'Koffi Jean',
                        'specialite' => 'Physique-Chimie'
                    ],
                    [
                        'email' => 'yao.amedee@ecole.ci',
                        'name' => 'Yao Amédée',
                        'specialite' => 'Histoire-Géographie'
                    ],
                    [
                        'email' => 'dibi.elisabeth@ecole.ci',
                        'name' => 'Dibi Elisabeth',
                        'specialite' => 'Anglais'
                    ],
                ];

                foreach ($enseignants as $enseignant) {
                    User::updateOrCreate(
                        ['email' => $enseignant['email']],
                        [
                            'name' => $enseignant['name'],
                            'password' => Hash::make('Enseignant@2024!'),
                            'email_verified_at' => now(),
                            'role_id' => $roles['enseignant']->id ?? 6,
                            'etablissement_id' => $etablissements->first()->id,
                        ]
                    );
                    $this->command->info("✓ Enseignant créé: {$enseignant['name']} ({$enseignant['specialite']})");
                }
            }

            // ============================================
            // PARENTS D'ÉLÈVES
            // ============================================
            $parents = [
                [
                    'email' => 'parent.traore@ecole.ci',
                    'name' => 'Traore Moussa',
                    'telephone' => '0708091011'
                ],
                [
                    'email' => 'parent.soro@ecole.ci',
                    'name' => 'Soro Awa',
                    'telephone' => '0758493021'
                ],
                [
                    'email' => 'parent.ouattara@ecole.ci',
                    'name' => 'Ouattara Fatou',
                    'telephone' => '0123456789'
                ],
            ];

            foreach ($parents as $parent) {
                User::updateOrCreate(
                    ['email' => $parent['email']],
                    [
                        'name' => $parent['name'],
                        'password' => Hash::make('Parent@2024!'),
                        'email_verified_at' => now(),
                        'role_id' => $roles['parent']->id ?? 7,
                        'etablissement_id' => $etablissements->first()->id ?? null,
                        'telephone' => $parent['telephone'],
                    ]
                );
                $this->command->info("✓ Parent créé: {$parent['name']}");
            }

            // ============================================
            // ÉLÈVES (comptes pour le suivi en ligne)
            // ============================================
            $eleves = [
                [
                    'email' => 'eleve.kouadio@ecole.ci',
                    'name' => 'Kouadio Paul Junior',
                    'matricule' => 'EL2025001'
                ],
                [
                    'email' => 'eleve.konan@ecole.ci',
                    'name' => 'Konan Marie-Ange',
                    'matricule' => 'EL2025002'
                ],
                [
                    'email' => 'eleve.koffi@ecole.ci',
                    'name' => 'Koffi Jean-Philippe',
                    'matricule' => 'EL2025003'
                ],
            ];

            foreach ($eleves as $eleve) {
                User::updateOrCreate(
                    ['email' => $eleve['email']],
                    [
                        'name' => $eleve['name'],
                        'password' => Hash::make('Eleve@2024!'),
                        'email_verified_at' => now(),
                        'role_id' => $roles['eleve']->id ?? 8,
                        'etablissement_id' => $etablissements->first()->id ?? null,
                    ]
                );
                $this->command->info("✓ Élève créé: {$eleve['name']}");
            }

            // ============================================
            // RÉCAPITULATIF
            // ============================================
            $this->command->newLine();
            $this->command->info('╔══════════════════════════════════════════════════════════╗');
            $this->command->info('║     🇨🇮 SYSCOL - SYSTÈME SCOLAIRE DE CÔTE D\'IVOIRE      ║');
            $this->command->info('╚══════════════════════════════════════════════════════════╝');
            $this->command->newLine();
            
            $this->command->info('IDENTIFIANTS DE CONNEXION :');
            $this->command->info('──────────────────────────────────────────────────────');
            $this->command->info('Super Admin MENET        : super.admin@syscol.ci / Super@2024!Secure');
            $this->command->info('Admin Abidjan            : admin.abidjan@syscol.ci / Admin@2024!');
            $this->command->info('Admin Bouaké             : admin.bouake@syscol.ci / Admin@2024!');
            $this->command->info('Directeur Études Abidjan : directeur.abidjan@syscol.ci / Directeur@2024!');
            $this->command->info('CPE Abidjan              : cpe.abidjan@syscol.ci / CPE@2024!');
            $this->command->info('Comptable Abidjan        : comptable.abidjan@syscol.ci / Compta@2024!');
            $this->command->info('Enseignant (Maths)      : kouadio.paul@ecole.ci / Enseignant@2024!');
            $this->command->info('Parent d\'élève           : parent.traore@ecole.ci / Parent@2024!');
            $this->command->info('Élève                    : eleve.kouadio@ecole.ci / Eleve@2024!');
            $this->command->info('──────────────────────────────────────────────────────');
            
            $this->command->newLine();
            $this->command->info('Administrateurs et utilisateurs créés avec succès !');
            $this->command->info('   Les logs d\'activité ont été temporairement désactivés pendant le seeder.');
            
        } catch (\Exception $e) {
            $this->command->error('Erreur : ' . $e->getMessage());
            throw $e;
        } finally {
            // Réactiver les logs quoi qu'il arrive
            $this->enableActivityLogging();
        }
    }
    
    /**
     * Désactiver temporairement les logs d'activité
     */
    private function disableActivityLogging(): void
    {
        if (class_exists('\Spatie\Activitylog\ActivitylogServiceProvider')) {
            Config::set('activitylog.enabled', false);
            $this->command->line('Logs d\'activité désactivés temporairement...');
        }
    }
    
    /**
     * Réactiver les logs d'activité
     */
    private function enableActivityLogging(): void
    {
        if (class_exists('\Spatie\Activitylog\ActivitylogServiceProvider')) {
            Config::set('activitylog.enabled', true);
            $this->command->line('Logs d\'activité réactivés');
        }
    }
}