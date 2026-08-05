<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🇨🇮 Création des rôles du système éducatif ivoirien...');
        
        $roles = [
            // ============================================
            // ADMINISTRATION CENTRALE (MENET)
            // ============================================
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrateur MENET',
                'description' => 'Administration centrale du Ministère de l\'Éducation Nationale',
                'level' => 100,
            ],
            [
                'name' => 'inspecteur_general',
                'display_name' => 'Inspecteur Général',
                'description' => 'Inspection Générale de l\'Éducation Nationale',
                'level' => 95,
            ],
            
            // ============================================
            // DIRECTIONS RÉGIONALES (DRENET)
            // ============================================
            [
                'name' => 'directeur_regional',
                'display_name' => 'Directeur Régional DRENET',
                'description' => 'Direction Régionale de l\'Éducation Nationale',
                'level' => 90,
            ],
            [
                'name' => 'inspecteur_iep',
                'display_name' => 'Inspecteur IEP',
                'description' => 'Inspection de l\'Enseignement Primaire',
                'level' => 85,
            ],
            
            // ============================================
            // ADMINISTRATION ÉTABLISSEMENT
            // ============================================
            [
                'name' => 'proviseur',
                'display_name' => 'Proviseur',
                'description' => 'Chef d\'établissement de lycée',
                'level' => 80,
            ],
            [
                'name' => 'principal',
                'display_name' => 'Principal',
                'description' => 'Chef d\'établissement de collège',
                'level' => 80,
            ],
            [
                'name' => 'directeur_ecole',
                'display_name' => 'Directeur d\'École',
                'description' => 'Directeur d\'école primaire',
                'level' => 75,
            ],
            
            // ✅ NOUVEAU : Admin Établissement (pour les DRENET)
            [
                'name' => 'admin_etablissement',
                'display_name' => 'Admin Établissement',
                'description' => 'Administrateur d\'établissement scolaire',
                'level' => 80,
            ],
            
            // ============================================
            // GESTION PÉDAGOGIQUE
            // ============================================
            [
                'name' => 'directeur_etudes',
                'display_name' => 'Directeur des Études',
                'description' => 'Coordination pédagogique et emplois du temps',
                'level' => 70,
            ],
            [
                'name' => 'censeur',
                'display_name' => 'Censeur',
                'description' => 'Gestion de la discipline et de la scolarité',
                'level' => 65,
            ],
            [
                'name' => 'surveillant_general',
                'display_name' => 'Surveillant Général',
                'description' => 'Encadrement des élèves et discipline',
                'level' => 60,
            ],
            
            // ============================================
            // VIE SCOLAIRE
            // ============================================
            [
                'name' => 'cpe',
                'display_name' => 'Conseiller Principal d\'Éducation',
                'description' => 'CPE - Vie scolaire et suivi des élèves',
                'level' => 60,
            ],
            [
                'name' => 'surveillant',
                'display_name' => 'Surveillant',
                'description' => 'Encadrement quotidien des élèves',
                'level' => 45,
            ],
            
            // ============================================
            // ADMINISTRATION FINANCIÈRE
            // ============================================
            [
                'name' => 'intendant',
                'display_name' => 'Intendant',
                'description' => 'Gestion financière et matérielle',
                'level' => 55,
            ],
            [
                'name' => 'comptable',
                'display_name' => 'Comptable',
                'description' => 'Gestion comptable et frais de scolarité',
                'level' => 50,
            ],
            [
                'name' => 'secretaire',
                'display_name' => 'Secrétaire',
                'description' => 'Gestion administrative et scolarité',
                'level' => 45,
            ],
            
            // ============================================
            // PERSONNEL ENSEIGNANT
            // ============================================
            [
                'name' => 'enseignant',
                'display_name' => 'Enseignant',
                'description' => 'Professeur de collège/lycée',
                'level' => 40,
            ],
            [
                'name' => 'instituteur',
                'display_name' => 'Instituteur',
                'description' => 'Enseignant du primaire',
                'level' => 40,
            ],
            [
                'name' => 'maitre_parent',
                'display_name' => 'Maître Parent',
                'description' => 'Enseignant communautaire',
                'level' => 35,
            ],
            
            // ============================================
            // AUTRES ACTEURS
            // ============================================
            [
                'name' => 'parent',
                'display_name' => 'Parent d\'élève',
                'description' => 'Suivi de la scolarité des enfants',
                'level' => 20,
            ],
            [
                'name' => 'eleve',
                'display_name' => 'Élève',
                'description' => 'Accès à son espace personnel',
                'level' => 10,
            ],
            [
                'name' => 'tuteur',
                'display_name' => 'Tuteur légal',
                'description' => 'Responsable légal de l\'élève',
                'level' => 20,
            ],
        ];

        $count = 0;
        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
            $count++;
        }

        // Statistiques
        $stats = [
            'total' => $count,
            'administration' => Role::where('level', '>=', 70)->count(),
            'pedagogie' => Role::whereBetween('level', [40, 69])->count(),
            'autres' => Role::where('level', '<', 40)->count(),
        ];

        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════╗');
        $this->command->info('║   🇨🇮 RÔLES DU SYSTÈME ÉDUCATIF IVOIRIEN (MENET)        ║');
        $this->command->info('╚══════════════════════════════════════════════════════════╝');
        $this->command->info("   Total rôles: \033[32m{$stats['total']}\033[0m");
        
        // Afficher la liste complète des rôles créés
        $this->command->newLine();
        $this->command->info('📋 LISTE DES RÔLES CRÉÉS :');
        $data = [];
        $allRoles = Role::orderBy('level', 'desc')->get();
        foreach ($allRoles as $role) {
            $data[] = [$role->name, $role->display_name, $role->level];
        }
        $this->command->table(['Identifiant', 'Nom affiché', 'Niveau'], $data);

        // Hiérarchie visuelle
        $this->command->newLine();
        $this->command->info('📈 HIÉRARCHIE DES NIVEAUX :');
        $this->command->line('   100 ── Super Admin MENET');
        $this->command->line('   95  ── Inspecteur Général');
        $this->command->line('   90  ── Directeur Régional DRENET');
        $this->command->line('   85  ── Inspecteur IEP');
        $this->command->line('   80  ── Proviseur / Principal / Admin Établissement');
        $this->command->line('   75  ── Directeur d\'École');
        $this->command->line('   70  ── Directeur des Études');
        $this->command->line('   65  ── Censeur');
        $this->command->line('   60  ── CPE / Surveillant Général');
        $this->command->line('   55  ── Intendant');
        $this->command->line('   50  ── Comptable');
        $this->command->line('   45  ── Surveillant / Secrétaire');
        $this->command->line('   40  ── Enseignant / Instituteur');
        $this->command->line('   35  ── Maître Parent');
        $this->command->line('   20  ── Parent / Tuteur');
        $this->command->line('   10  ── Élève');

        $this->command->newLine();
        $this->command->info('✅ Rôles du système éducatif ivoirien créés avec succès !');
        
        // Avertissement sur les rôles système
        $this->command->newLine();
        $this->command->warn('⚠️  Note: Les rôles système suivants sont protégés :');
        $this->command->line('   • super_admin');
        $this->command->line('   • admin_etablissement');
        $this->command->line('   • enseignant');
        $this->command->line('   • eleve');
        $this->command->line('   • parent');
    }
}