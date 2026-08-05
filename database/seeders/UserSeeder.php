<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Etablissement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Désactiver temporairement les logs d'activité
        $this->disableActivityLogging();
        
        try {
            $this->command->info('🇨🇮 Création des utilisateurs ivoiriens...');
            
            $roles = Role::all()->keyBy('name');
            $etablissements = Etablissement::all();
            
            if ($roles->isEmpty()) {
                $this->command->error('❌ Aucun rôle trouvé. Exécutez d\'abord RoleSeeder.');
                return;
            }

            if ($etablissements->isEmpty()) {
                $this->command->warn('⚠️ Aucun établissement trouvé. Certains utilisateurs seront créés sans établissement.');
            }

            // ============================================
            // ENSEIGNANTS
            // ============================================
            $enseignants = [
                [
                    'name' => 'Kouadio Paul',
                    'email' => 'kouadio.paul@ecole.ci',
                    'password' => 'Enseignant@2024!',
                    'role' => 'enseignant',
                    'telephone' => '0701020304',
                ],
                [
                    'name' => 'Konan Marie',
                    'email' => 'konan.marie@ecole.ci',
                    'password' => 'Enseignant@2024!',
                    'role' => 'enseignant',
                    'telephone' => '0504050607',
                ],
                [
                    'name' => 'Koffi Jean',
                    'email' => 'koffi.jean@ecole.ci',
                    'password' => 'Enseignant@2024!',
                    'role' => 'enseignant',
                    'telephone' => '0108091011',
                ],
                [
                    'name' => 'Yao Amédée',
                    'email' => 'yao.amedee@ecole.ci',
                    'password' => 'Enseignant@2024!',
                    'role' => 'enseignant',
                    'telephone' => '0711121314',
                ],
                [
                    'name' => 'Dibi Elisabeth',
                    'email' => 'dibi.elisabeth@ecole.ci',
                    'password' => 'Enseignant@2024!',
                    'role' => 'enseignant',
                    'telephone' => '0515161718',
                ],
                [
                    'name' => 'N\'Guessan François',
                    'email' => 'nguessan.francois@ecole.ci',
                    'password' => 'Enseignant@2024!',
                    'role' => 'enseignant',
                    'telephone' => '0119202122',
                ],
                [
                    'name' => 'Aka Martine',
                    'email' => 'aka.martine@ecole.ci',
                    'password' => 'Enseignant@2024!',
                    'role' => 'enseignant',
                    'telephone' => '0723242526',
                ],
            ];

            // ============================================
            // PARENTS D'ÉLÈVES
            // ============================================
            $parents = [
                [
                    'name' => 'Traoré Moussa',
                    'email' => 'traore.moussa@famille.ci',
                    'password' => 'Parent@2024!',
                    'role' => 'parent',
                    'telephone' => '0527282930',
                ],
                [
                    'name' => 'Soro Awa',
                    'email' => 'soro.awa@famille.ci',
                    'password' => 'Parent@2024!',
                    'role' => 'parent',
                    'telephone' => '0131323334',
                ],
                [
                    'name' => 'Ouattara Fatou',
                    'email' => 'ouattara.fatou@famille.ci',
                    'password' => 'Parent@2024!',
                    'role' => 'parent',
                    'telephone' => '0735363738',
                ],
                [
                    'name' => 'Bamba Souleymane',
                    'email' => 'bamba.souleymane@famille.ci',
                    'password' => 'Parent@2024!',
                    'role' => 'parent',
                    'telephone' => '0539404142',
                ],
                [
                    'name' => 'Coulibaly Aminata',
                    'email' => 'coulibaly.aminata@famille.ci',
                    'password' => 'Parent@2024!',
                    'role' => 'parent',
                    'telephone' => '0143444546',
                ],
                [
                    'name' => 'Diaby Karim',
                    'email' => 'diaby.karim@famille.ci',
                    'password' => 'Parent@2024!',
                    'role' => 'parent',
                    'telephone' => '0747484950',
                ],
            ];

            // ============================================
            // ÉLÈVES
            // ============================================
            $eleves = [
                [
                    'name' => 'Kouadio Paul Junior',
                    'email' => 'eleve.kouadio@ecole.ci',
                    'password' => 'Eleve@2024!',
                    'role' => 'eleve',
                ],
                [
                    'name' => 'Konan Marie-Ange',
                    'email' => 'eleve.konan@ecole.ci',
                    'password' => 'Eleve@2024!',
                    'role' => 'eleve',
                ],
                [
                    'name' => 'Koffi Jean-Philippe',
                    'email' => 'eleve.koffi@ecole.ci',
                    'password' => 'Eleve@2024!',
                    'role' => 'eleve',
                ],
                [
                    'name' => 'Yao Christelle',
                    'email' => 'eleve.yao@ecole.ci',
                    'password' => 'Eleve@2024!',
                    'role' => 'eleve',
                ],
                [
                    'name' => 'Traoré Issouf',
                    'email' => 'eleve.traore@ecole.ci',
                    'password' => 'Eleve@2024!',
                    'role' => 'eleve',
                ],
                [
                    'name' => 'Soro Fatoumata',
                    'email' => 'eleve.soro@ecole.ci',
                    'password' => 'Eleve@2024!',
                    'role' => 'eleve',
                ],
            ];

            // ============================================
            // AUTRES PERSONNELS
            // ============================================
            $personnels = [
                [
                    'name' => 'Kra Alain',
                    'email' => 'kra.alain@etablissement.ci',
                    'password' => 'Secretariat@2024!',
                    'role' => 'secretaire',
                    'telephone' => '0551525354',
                ],
                [
                    'name' => 'Goly Armand',
                    'email' => 'goly.armand@etablissement.ci',
                    'password' => 'Intendance@2024!',
                    'role' => 'intendant',
                    'telephone' => '0155565758',
                ],
                [
                    'name' => 'Vanié Jacqueline',
                    'email' => 'vanie.jacqueline@etablissement.ci',
                    'password' => 'Comptabilite@2024!',
                    'role' => 'comptable',
                    'telephone' => '0759606162',
                ],
                [
                    'name' => 'Seka David',
                    'email' => 'seka.david@etablissement.ci',
                    'password' => 'Surveillance@2024!',
                    'role' => 'surveillant',
                    'telephone' => '0563646566',
                ],
            ];

            // Fusionner tous les utilisateurs
            $allUsers = array_merge($enseignants, $parents, $eleves, $personnels);
            
            $totalCreations = 0;
            $parRole = [];

            foreach ($allUsers as $userData) {
                if (!isset($roles[$userData['role']])) {
                    $this->command->warn("Rôle '{$userData['role']}' non trouvé - utilisateur ignoré");
                    continue;
                }

                // Vérifier si l'email existe déjà
                if (User::where('email', $userData['email'])->exists()) {
                    $this->command->warn("Email déjà existant: {$userData['email']}");
                    continue;
                }

                $etablissementId = null;
                if ($etablissements->isNotEmpty()) {
                    // Répartir les utilisateurs entre les établissements
                    $etablissementId = $etablissements->random()->id;
                }

                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                    'role_id' => $roles[$userData['role']]->id,
                    'etablissement_id' => $etablissementId,
                    'telephone' => $userData['telephone'] ?? null,
                    'is_active' => true,
                ]);

                $parRole[$userData['role']] = ($parRole[$userData['role']] ?? 0) + 1;
                $totalCreations++;
                $this->command->info("✓ {$userData['email']} ({$userData['role']})");
            }

            // Statistiques finales
            $this->command->newLine();
            $this->command->info('╔════════════════════════════════════════════════════╗');
            $this->command->info('║   🇨🇮 UTILISATEURS DU SYSTÈME SCOLAIRE IVOIRIEN  ║');
            $this->command->info('╚════════════════════════════════════════════════════╝');
            $this->command->info("   👥 Total utilisateurs créés: \033[32m{$totalCreations}\033[0m");
            
            $this->command->newLine();
            $this->command->info('RÉPARTITION PAR RÔLE :');
            
            $data = [];
            foreach ($parRole as $role => $count) {
                $roleName = $roles[$role]->display_name ?? $role;
                $data[] = [$roleName, $count, round(($count / $totalCreations) * 100, 1) . '%'];
            }
            
            $this->command->table(['Rôle', 'Nombre', 'Pourcentage'], $data);

            // Afficher quelques exemples
            $this->command->newLine();
            $this->command->info('EXEMPLES D\'UTILISATEURS CRÉÉS :');
            $exemples = User::with('role', 'etablissement')->inRandomOrder()->take(5)->get();
            foreach ($exemples as $user) {
                $etab = $user->etablissement ? " - {$user->etablissement->nom}" : '';
                $this->command->line("   • {$user->name} ({$user->role->display_name}){$etab}");
                $this->command->line("     {$user->email}");
            }

            $this->command->newLine();
            $this->command->info('✅ Utilisateurs standards créés avec succès !');
            
            // Avertissement pour les mots de passe
            $this->command->newLine();
            $this->command->warn('📝 Note: Les mots de passe sont différents selon le rôle :');
            $this->command->line('   • Enseignants: Enseignant@2024!');
            $this->command->line('   • Parents: Parent@2024!');
            $this->command->line('   • Élèves: Eleve@2024!');
            $this->command->line('   • Personnels: Secretaria@2024!, Intendance@2024!, etc.');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Erreur : ' . $e->getMessage());
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
            $this->command->line('⏸️  Logs d\'activité désactivés temporairement...');
        }
    }
    
    /**
     * Réactiver les logs d'activité
     */
    private function enableActivityLogging(): void
    {
        if (class_exists('\Spatie\Activitylog\ActivitylogServiceProvider')) {
            Config::set('activitylog.enabled', true);
            $this->command->line('▶️  Logs d\'activité réactivés');
        }
    }
}