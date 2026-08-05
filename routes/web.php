<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;

// SUPER ADMIN
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EtablissementController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\StatistiqueController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;

// ADMIN ÉTABLISSEMENT
use App\Http\Controllers\Etablissement\DashboardController as EtablissementDashboardController;
use App\Http\Controllers\Etablissement\ClasseController;
use App\Http\Controllers\Etablissement\EnseignantController as EtablissementEnseignantController;
use App\Http\Controllers\Etablissement\EleveController;
use App\Http\Controllers\Etablissement\MatiereController as EtablissementMatiereController;
use App\Http\Controllers\Etablissement\EmploiTempsController as EtablissementEmploiTempsController;
use App\Http\Controllers\Etablissement\AnneeScolaireController;
use App\Http\Controllers\Etablissement\UtilisateurController as EtablissementUtilisateurController;
use App\Http\Controllers\Etablissement\ParametreController as EtablissementParametreController;
use App\Http\Controllers\Etablissement\AbsenceController as EtablissementAbsenceController;
use App\Http\Controllers\Etablissement\NoteController as EtablissementNoteController;
use App\Http\Controllers\Etablissement\TrimestreController;
use App\Http\Controllers\Etablissement\MessageController as EtablissementMessageController;

// DIRECTEUR DES ÉTUDES
use App\Http\Controllers\Directeur\DashboardController as DirecteurDashboardController;
use App\Http\Controllers\Directeur\EmploiTempsController as DirecteurEmploiTempsController;
use App\Http\Controllers\Directeur\ExamenController as DirecteurExamenController;
use App\Http\Controllers\Directeur\BulletinController as DirecteurBulletinController;
use App\Http\Controllers\Directeur\ProgrammeController as DirecteurProgrammeController;
use App\Http\Controllers\Directeur\ClasseController as DirecteurClasseController;
use App\Http\Controllers\Directeur\MatiereController as DirecteurMatiereController;

// CPE
use App\Http\Controllers\Cpe\DashboardController as CpeDashboardController;
use App\Http\Controllers\Cpe\AbsenceController as CpeAbsenceController;
use App\Http\Controllers\Cpe\DisciplineController as CpeDisciplineController;
use App\Http\Controllers\Cpe\RetardController as CpeRetardController;
use App\Http\Controllers\Cpe\SanctionController as CpeSanctionController;
use App\Http\Controllers\Cpe\PresenceController as CpePresenceController;

// COMPTABLE
use App\Http\Controllers\Comptable\DashboardController as ComptableDashboardController;
use App\Http\Controllers\Comptable\FraisScolariteController;
use App\Http\Controllers\Comptable\PaiementController;
use App\Http\Controllers\Comptable\FactureController;
use App\Http\Controllers\Comptable\DepenseController;
use App\Http\Controllers\Comptable\RapportFinancierController;
use App\Http\Controllers\Comptable\BourseController;
use App\Http\Controllers\Comptable\ImpayeController; 

// ENSEIGNANT
use App\Http\Controllers\Enseignant\DashboardController as EnseignantDashboardController;
use App\Http\Controllers\Enseignant\ClasseController as EnseignantClasseController;
use App\Http\Controllers\Enseignant\NoteController as EnseignantNoteController;
use App\Http\Controllers\Enseignant\PresenceController as EnseignantPresenceController;
use App\Http\Controllers\Enseignant\EmploiTempsController as EnseignantEmploiTempsController;
use App\Http\Controllers\Enseignant\MatiereController as EnseignantMatiereController;
use App\Http\Controllers\Enseignant\EvaluationController as EnseignantEvaluationController;
use App\Http\Controllers\Enseignant\RapportController as EnseignantRapportController;

// PARENT
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\Parent\EnfantController;
use App\Http\Controllers\Parent\BulletinController as ParentBulletinController;
use App\Http\Controllers\Parent\PaiementController as ParentPaiementController;
use App\Http\Controllers\Parent\CommunicationController;
use App\Http\Controllers\Parent\NoteController as ParentNoteController;
use App\Http\Controllers\Parent\EmploiTempsController as ParentEmploiTempsController;
use App\Http\Controllers\Parent\AbsenceController as ParentAbsenceController;

// ÉLÈVE
use App\Http\Controllers\Eleve\DashboardController as EleveDashboardController;
use App\Http\Controllers\Eleve\NoteController as EleveNoteController;
use App\Http\Controllers\Eleve\EmploiTempsController as EleveEmploiTempsController;
use App\Http\Controllers\Eleve\DevoirController;
use App\Http\Controllers\Eleve\CoursController;
use App\Http\Controllers\Eleve\AbsenceController as EleveAbsenceController;
use App\Http\Controllers\Eleve\BulletinController as EleveBulletinController;
use App\Http\Controllers\Eleve\ProfilController as EleveProfilController;

// Routes d'authentification (PUBLIQUES)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirection de la page d'accueil
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    
    // ============================================
    // PROFIL UTILISATEUR - Routes ajoutées
    // ============================================
    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/modifier', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('photo');
    });
    
    // DASHBOARD PRINCIPAL - Redirection intelligente
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        if (!$user->role) {
            return redirect()->route('login')->with('error', 'Rôle non défini');
        }
        
        return match($user->role->name) {
            'super_admin' => redirect()->route('admin.dashboard'),
            'admin_etablissement' => redirect()->route('etablissement.dashboard', ['etablissement' => $user->etablissement_id]),
            'directeur_etudes' => redirect()->route('directeur.dashboard'),
            'cpe' => redirect()->route('cpe.dashboard'),
            'comptable' => redirect()->route('comptable.dashboard'),
            'enseignant' => redirect()->route('enseignant.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            'eleve' => redirect()->route('eleve.dashboard'),
            default => redirect()->route('login')->with('error', 'Rôle non reconnu'),
        };
    })->name('dashboard');
    
    // ============================================
    // SUPER ADMIN - Accès complet
    // ============================================
    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // ÉTABLISSEMENTS
        Route::get('/etablissements', [EtablissementController::class, 'index'])->name('etablissements');
        Route::get('/etablissements/create', [EtablissementController::class, 'create'])->name('etablissements.create');
        Route::post('/etablissements', [EtablissementController::class, 'store'])->name('etablissements.store');
        Route::get('/etablissements/{id}', [EtablissementController::class, 'show'])->name('etablissements.show');
        Route::get('/etablissements/{id}/edit', [EtablissementController::class, 'edit'])->name('etablissements.edit');
        Route::put('/etablissements/{id}', [EtablissementController::class, 'update'])->name('etablissements.update');
        Route::delete('/etablissements/{id}', [EtablissementController::class, 'destroy'])->name('etablissements.destroy');
        Route::post('/etablissements/{id}/toggle-status', [EtablissementController::class, 'toggleStatus'])->name('etablissements.toggle-status');
        Route::get('/etablissements/export', [EtablissementController::class, 'export'])->name('etablissements.export');
        Route::get('/etablissements/search', [EtablissementController::class, 'search'])->name('etablissements.search');
        Route::get('/etablissements/statistiques', [EtablissementController::class, 'statistiques'])->name('etablissements.statistiques');
        
        // UTILISATEURS
        Route::get('/utilisateurs', [AdminUserController::class, 'index'])->name('users');
        Route::get('/utilisateurs/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/utilisateurs', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/utilisateurs/{id}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/utilisateurs/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/utilisateurs/{id}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/utilisateurs/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::post('/utilisateurs/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/utilisateurs/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/utilisateurs/export', [AdminUserController::class, 'export'])->name('users.export');
        Route::get('/utilisateurs/search', [AdminUserController::class, 'search'])->name('users.search');
        Route::post('/utilisateurs/bulk-action', [AdminUserController::class, 'bulkAction'])->name('users.bulk-action');
        
        // STATISTIQUES
        Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques');
        Route::get('/statistiques/export', [StatistiqueController::class, 'export'])->name('statistiques.export');
        Route::get('/statistiques/chart-data', [StatistiqueController::class, 'chartData'])->name('statistiques.chart-data');
        
        // RÔLES
        Route::get('/roles', [RoleController::class, 'index'])->name('roles');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/{role}/users', [RoleController::class, 'users'])->name('roles.users');
        Route::get('/roles/{role}/duplicate', [RoleController::class, 'duplicate'])->name('roles.duplicate');
        Route::post('/roles/{role}/update-level', [RoleController::class, 'updateLevel'])->name('roles.update-level');
        Route::get('/roles/export', [RoleController::class, 'export'])->name('roles.export');
        Route::get('/roles/search', [RoleController::class, 'search'])->name('roles.search');
        
        // CONFIGURATION SYSTÈME
        Route::prefix('configurations')->name('configurations.')->group(function () {
            Route::get('/', [ConfigurationController::class, 'index'])->name('index');
            Route::get('/general', [ConfigurationController::class, 'general'])->name('general');
            Route::post('/general', [ConfigurationController::class, 'updateGeneral'])->name('general.update');
            Route::get('/auth', [ConfigurationController::class, 'auth'])->name('auth');
            Route::post('/auth', [ConfigurationController::class, 'updateAuth'])->name('auth.update');
            Route::get('/modules', [ConfigurationController::class, 'modules'])->name('modules');
            Route::post('/modules', [ConfigurationController::class, 'updateModules'])->name('modules.update');
            Route::get('/mail', [ConfigurationController::class, 'mail'])->name('mail');
            Route::post('/mail', [ConfigurationController::class, 'updateMail'])->name('mail.update');
            Route::post('/mail/test', [ConfigurationController::class, 'testMail'])->name('mail.test');
            Route::get('/maintenance', [ConfigurationController::class, 'maintenance'])->name('maintenance');
            Route::post('/maintenance/enable', [ConfigurationController::class, 'enableMaintenance'])->name('maintenance.enable');
            Route::post('/maintenance/disable', [ConfigurationController::class, 'disableMaintenance'])->name('maintenance.disable');
            Route::get('/system', [ConfigurationController::class, 'system'])->name('system');
            Route::get('/backup', [ConfigurationController::class, 'backup'])->name('backup');
            Route::get('/backups', [ConfigurationController::class, 'backups'])->name('backups');
            Route::get('/backups/download/{filename}', [ConfigurationController::class, 'downloadBackup'])->name('backups.download');
            Route::delete('/backups/{filename}', [ConfigurationController::class, 'deleteBackup'])->name('backups.delete');
            Route::get('/clear-cache', [ConfigurationController::class, 'clearCache'])->name('clear-cache');
            Route::get('/optimize', [ConfigurationController::class, 'optimize'])->name('optimize');
        });
        
        // JOURNAUX D'ACTIVITÉ (LOGS)
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [LogController::class, 'index'])->name('index');
            Route::get('/export', [LogController::class, 'export'])->name('export');
            Route::get('/chart', [LogController::class, 'chartData'])->name('chart');
            Route::get('/{id}', [LogController::class, 'show'])->name('show');
            Route::delete('/{id}', [LogController::class, 'destroy'])->name('destroy');
            Route::post('/clear', [LogController::class, 'clear'])->name('clear');
        });
        
        // ============================================
        // COMMUNICATIONS - SUPER ADMIN
        // Reçoit les messages des admins d'établissement
        // ============================================
        Route::prefix('communications')->name('communications.')->group(function () {
            Route::get('/', [AdminMessageController::class, 'index'])->name('index');
            Route::get('/create', [AdminMessageController::class, 'create'])->name('create');
            Route::post('/send', [AdminMessageController::class, 'send'])->name('send');
            Route::get('/{id}', [AdminMessageController::class, 'show'])->name('show');
            Route::post('/{id}/reply', [AdminMessageController::class, 'reply'])->name('reply');
            Route::delete('/{id}', [AdminMessageController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/mark-read', [AdminMessageController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [AdminMessageController::class, 'markAllAsRead'])->name('mark-all-read');
        });
    });
    
    // ============================================
    // ADMIN ÉTABLISSEMENT
    // ============================================
    Route::middleware(['role:admin_etablissement'])->prefix('etablissement')->name('etablissement.')->group(function () {
        
        // DASHBOARD
        Route::get('/dashboard/{etablissement}', [EtablissementDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/refresh/{etablissement}', [EtablissementDashboardController::class, 'refresh'])->name('dashboard.refresh');
        Route::get('/dashboard/export/{etablissement}', [EtablissementDashboardController::class, 'exportPdf'])->name('dashboard.export');
        
        // CLASSES
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [ClasseController::class, 'index'])->name('index');
            Route::get('/create', [ClasseController::class, 'create'])->name('create');
            Route::post('/', [ClasseController::class, 'store'])->name('store');
            Route::get('/{classe}', [ClasseController::class, 'show'])->name('show');
            Route::get('/{classe}/edit', [ClasseController::class, 'edit'])->name('edit');
            Route::put('/{classe}', [ClasseController::class, 'update'])->name('update');
            Route::delete('/{classe}', [ClasseController::class, 'destroy'])->name('destroy');
            Route::get('/{classe}/eleves', [ClasseController::class, 'eleves'])->name('eleves');
            Route::get('/export', [ClasseController::class, 'export'])->name('export');
            Route::get('/chart-data', [ClasseController::class, 'chartData'])->name('chart');
        });
        
        // ENSEIGNANTS
        Route::prefix('enseignants')->name('enseignants.')->group(function () {
            Route::get('/', [EtablissementEnseignantController::class, 'index'])->name('index');
            Route::get('/create', [EtablissementEnseignantController::class, 'create'])->name('create');
            Route::post('/', [EtablissementEnseignantController::class, 'store'])->name('store');
            Route::get('/{enseignant}', [EtablissementEnseignantController::class, 'show'])->name('show');
            Route::get('/{enseignant}/edit', [EtablissementEnseignantController::class, 'edit'])->name('edit');
            Route::put('/{enseignant}', [EtablissementEnseignantController::class, 'update'])->name('update');
            Route::delete('/{enseignant}', [EtablissementEnseignantController::class, 'destroy'])->name('destroy');
            Route::get('/export', [EtablissementEnseignantController::class, 'export'])->name('export');
            Route::get('/{enseignant}/emploi-temps', [EtablissementEnseignantController::class, 'emploiTemps'])->name('emploi-temps');
        });
        
        // ÉLÈVES
        Route::prefix('eleves')->name('eleves.')->group(function () {
            Route::get('/', [EleveController::class, 'index'])->name('index');
            Route::get('/create', [EleveController::class, 'create'])->name('create');
            Route::post('/', [EleveController::class, 'store'])->name('store');
            Route::get('/{eleve}', [EleveController::class, 'show'])->name('show');
            Route::get('/{eleve}/edit', [EleveController::class, 'edit'])->name('edit');
            Route::put('/{eleve}', [EleveController::class, 'update'])->name('update');
            Route::delete('/{eleve}', [EleveController::class, 'destroy'])->name('destroy');
            Route::get('/export', [EleveController::class, 'export'])->name('export');
            Route::get('/stats', [EleveController::class, 'statistiques'])->name('stats');
            Route::get('/{eleve}/notes', [EleveController::class, 'notes'])->name('notes');
        });
        
        // MATIÈRES
        Route::prefix('matieres')->name('matieres.')->group(function () {
            Route::get('/', [EtablissementMatiereController::class, 'index'])->name('index');
            Route::get('/create', [EtablissementMatiereController::class, 'create'])->name('create');
            Route::post('/', [EtablissementMatiereController::class, 'store'])->name('store');
            Route::get('/{matiere}/edit', [EtablissementMatiereController::class, 'edit'])->name('edit');
            Route::put('/{matiere}', [EtablissementMatiereController::class, 'update'])->name('update');
            Route::delete('/{matiere}', [EtablissementMatiereController::class, 'destroy'])->name('destroy');
        });
        
        // EMPLOIS DU TEMPS
        Route::prefix('emplois-temps')->name('emplois_temps.')->group(function () {
            Route::get('/', [EtablissementEmploiTempsController::class, 'index'])->name('index');
            Route::get('/create', [EtablissementEmploiTempsController::class, 'create'])->name('create');
            Route::post('/', [EtablissementEmploiTempsController::class, 'store'])->name('store');
            Route::get('/{emploiTemps}', [EtablissementEmploiTempsController::class, 'show'])->name('show');
            Route::get('/{emploiTemps}/edit', [EtablissementEmploiTempsController::class, 'edit'])->name('edit');
            Route::put('/{emploiTemps}', [EtablissementEmploiTempsController::class, 'update'])->name('update');
            Route::delete('/{emploiTemps}', [EtablissementEmploiTempsController::class, 'destroy'])->name('destroy');
            Route::get('/classe/{classeId}', [EtablissementEmploiTempsController::class, 'classe'])->name('classe');
            Route::get('/enseignant/{enseignantId}', [EtablissementEmploiTempsController::class, 'enseignant'])->name('enseignant');
            Route::get('/export', [EtablissementEmploiTempsController::class, 'export'])->name('export');
        });
        
        // TRIMESTRES
        Route::prefix('trimestres')->name('trimestres.')->group(function () {
            Route::get('/', [TrimestreController::class, 'index'])->name('index');
            Route::get('/create', [TrimestreController::class, 'create'])->name('create');
            Route::post('/create-manual', [TrimestreController::class, 'createManual'])->name('create-manual');
            Route::delete('/{id}', [TrimestreController::class, 'destroy'])->name('destroy');
        });
        
        // ANNÉES SCOLAIRES
        Route::prefix('annes-scolaires')->name('annes_scolaires.')->group(function () {
            Route::get('/', [AnneeScolaireController::class, 'index'])->name('index');
            Route::get('/create', [AnneeScolaireController::class, 'create'])->name('create');
            Route::post('/', [AnneeScolaireController::class, 'store'])->name('store');
            Route::get('/{annee}/edit', [AnneeScolaireController::class, 'edit'])->name('edit');
            Route::get('/{annee}', [AnneeScolaireController::class, 'show'])->name('show');
            Route::put('/{annee}', [AnneeScolaireController::class, 'update'])->name('update');
            Route::delete('/{annee}', [AnneeScolaireController::class, 'destroy'])->name('destroy');
            Route::post('/{annee}/set-current', [AnneeScolaireController::class, 'setCurrent'])->name('set-current');
        });
        
        // ABSENCES
        Route::prefix('absences')->name('absences.')->group(function () {
            Route::get('/', [EtablissementAbsenceController::class, 'index'])->name('index');
            Route::get('/create', [EtablissementAbsenceController::class, 'create'])->name('create');
            Route::post('/', [EtablissementAbsenceController::class, 'store'])->name('store');
            Route::get('/{absence}', [EtablissementAbsenceController::class, 'show'])->name('show');
            Route::get('/{absence}/edit', [EtablissementAbsenceController::class, 'edit'])->name('edit');
            Route::put('/{absence}', [EtablissementAbsenceController::class, 'update'])->name('update');
            Route::delete('/{absence}', [EtablissementAbsenceController::class, 'destroy'])->name('destroy');
            Route::post('/{absence}/justify', [EtablissementAbsenceController::class, 'justify'])->name('justify');
            Route::get('/export', [EtablissementAbsenceController::class, 'export'])->name('export');
        });
        
        // NOTES
        Route::prefix('notes')->name('notes.')->group(function () {
            Route::get('/', [EtablissementNoteController::class, 'index'])->name('index');
            Route::get('/create', [EtablissementNoteController::class, 'create'])->name('create');
            Route::post('/', [EtablissementNoteController::class, 'store'])->name('store');
            Route::get('/{note}', [EtablissementNoteController::class, 'show'])->name('show');
            Route::get('/{note}/edit', [EtablissementNoteController::class, 'edit'])->name('edit');
            Route::put('/{note}', [EtablissementNoteController::class, 'update'])->name('update');
            Route::delete('/{note}', [EtablissementNoteController::class, 'destroy'])->name('destroy');
            Route::get('/export', [EtablissementNoteController::class, 'export'])->name('export');
        });
        
        // UTILISATEURS (personnel de l'établissement)
        Route::prefix('utilisateurs')->name('utilisateurs.')->group(function () {
            Route::get('/', [EtablissementUtilisateurController::class, 'index'])->name('index');
            Route::get('/create', [EtablissementUtilisateurController::class, 'create'])->name('create');
            Route::post('/', [EtablissementUtilisateurController::class, 'store'])->name('store');
            Route::get('/{utilisateur}', [EtablissementUtilisateurController::class, 'show'])->name('show');
            Route::get('/{utilisateur}/edit', [EtablissementUtilisateurController::class, 'edit'])->name('edit');
            Route::put('/{utilisateur}', [EtablissementUtilisateurController::class, 'update'])->name('update');
            Route::put('/{utilisateur}/activate', [EtablissementUtilisateurController::class, 'activate'])->name('activate');
            Route::put('/{utilisateur}/deactivate', [EtablissementUtilisateurController::class, 'deactivate'])->name('deactivate');
            Route::post('/{utilisateur}/reset-password', [EtablissementUtilisateurController::class, 'resetPassword'])->name('reset-password');
            Route::get('/export', [EtablissementUtilisateurController::class, 'export'])->name('export');
            Route::delete('/{utilisateur}', [EtablissementUtilisateurController::class, 'destroy'])->name('destroy');
        });
        
        // PARAMÈTRES DE L'ÉTABLISSEMENT
        Route::prefix('parametres')->name('parametres.')->group(function () {
            Route::get('/', [EtablissementParametreController::class, 'index'])->name('index');
            Route::post('/general', [EtablissementParametreController::class, 'updateGeneral'])->name('update-general');
            Route::post('/infos', [EtablissementParametreController::class, 'updateInfos'])->name('update-infos');
            Route::post('/logo', [EtablissementParametreController::class, 'updateLogo'])->name('logo');
            Route::post('/horaires', [EtablissementParametreController::class, 'updateHoraires'])->name('update-horaires');
            Route::post('/notes-config', [EtablissementParametreController::class, 'updateNotesConfig'])->name('update-notes');
            Route::post('/absences-config', [EtablissementParametreController::class, 'updateAbsencesConfig'])->name('update-absences');
            Route::get('/rapport', [EtablissementParametreController::class, 'generateReport'])->name('rapport');
            Route::get('/rapport-pdf', [EtablissementParametreController::class, 'exportPDF'])->name('rapport-pdf');
            Route::post('/backup', [EtablissementParametreController::class, 'backup'])->name('backup');
            Route::post('/restore', [EtablissementParametreController::class, 'restore'])->name('restore');
        });
        
        // ============================================
        // COMMUNICATIONS - ADMIN ÉTABLISSEMENT
        // Reçoit les messages des parents
        // ============================================
        Route::prefix('communications')->name('communications.')->group(function () {
            Route::get('/', [EtablissementMessageController::class, 'index'])->name('index');
            Route::get('/create', [EtablissementMessageController::class, 'create'])->name('create');
            Route::post('/send', [EtablissementMessageController::class, 'send'])->name('send');
            Route::post('/transfer/{id}', [EtablissementMessageController::class, 'transferToSuperAdmin'])->name('transfer');
            Route::get('/{id}', [EtablissementMessageController::class, 'show'])->name('show');
            Route::post('/{id}/reply', [EtablissementMessageController::class, 'reply'])->name('reply');
            Route::delete('/{id}', [EtablissementMessageController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/mark-read', [EtablissementMessageController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [EtablissementMessageController::class, 'markAllAsRead'])->name('mark-all-read');
        });
    });
    
    // ============================================
    // DIRECTEUR DES ÉTUDES
    // ============================================
    Route::middleware(['role:directeur_etudes'])->prefix('directeur')->name('directeur.')->group(function () {
        Route::get('/dashboard', [DirecteurDashboardController::class, 'index'])->name('dashboard');
        Route::get('/emplois-temps', [DirecteurEmploiTempsController::class, 'index'])->name('emplois_temps');
        Route::get('/examens', [DirecteurExamenController::class, 'index'])->name('examens');
        Route::get('/bulletins', [DirecteurBulletinController::class, 'index'])->name('bulletins');
        Route::get('/programmes', [DirecteurProgrammeController::class, 'index'])->name('programmes');
        Route::get('/classes', [DirecteurClasseController::class, 'index'])->name('classes');
        Route::get('/matieres', [DirecteurMatiereController::class, 'index'])->name('matieres');
    });
    
    // ============================================
    // CPE
    // ============================================
    Route::middleware(['role:cpe'])->prefix('cpe')->name('cpe.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [CpeDashboardController::class, 'index'])->name('dashboard');
        
        // Absences
        Route::prefix('absences')->name('absences.')->group(function () {
            Route::get('/', [CpeAbsenceController::class, 'index'])->name('index');
            Route::get('/create', [CpeAbsenceController::class, 'create'])->name('create');
            Route::post('/', [CpeAbsenceController::class, 'store'])->name('store');
            Route::get('/{absence}', [CpeAbsenceController::class, 'show'])->name('show');
            Route::get('/{absence}/edit', [CpeAbsenceController::class, 'edit'])->name('edit');
            Route::put('/{absence}', [CpeAbsenceController::class, 'update'])->name('update');
            Route::delete('/{absence}', [CpeAbsenceController::class, 'destroy'])->name('destroy');
            Route::post('/{absence}/justify', [CpeAbsenceController::class, 'justify'])->name('justify');
            Route::get('/export', [CpeAbsenceController::class, 'export'])->name('export');
        });
        
        // Retards
        Route::prefix('retards')->name('retards.')->group(function () {
            Route::get('/', [CpeRetardController::class, 'index'])->name('index');
            Route::get('/create', [CpeRetardController::class, 'create'])->name('create');
            Route::post('/', [CpeRetardController::class, 'store'])->name('store');
            Route::get('/{retard}', [CpeRetardController::class, 'show'])->name('show');
            Route::get('/{retard}/edit', [CpeRetardController::class, 'edit'])->name('edit');
            Route::put('/{retard}', [CpeRetardController::class, 'update'])->name('update');
            Route::delete('/{retard}', [CpeRetardController::class, 'destroy'])->name('destroy');
            Route::get('/export', [CpeRetardController::class, 'export'])->name('export');
        });
        
        // Sanctions
        Route::prefix('sanctions')->name('sanctions.')->group(function () {
            Route::get('/', [CpeSanctionController::class, 'index'])->name('index');
            Route::get('/create', [CpeSanctionController::class, 'create'])->name('create');
            Route::post('/', [CpeSanctionController::class, 'store'])->name('store');
            Route::get('/{sanction}', [CpeSanctionController::class, 'show'])->name('show');
            Route::get('/{sanction}/edit', [CpeSanctionController::class, 'edit'])->name('edit');
            Route::put('/{sanction}', [CpeSanctionController::class, 'update'])->name('update');
            Route::delete('/{sanction}', [CpeSanctionController::class, 'destroy'])->name('destroy');
            Route::get('/export', [CpeSanctionController::class, 'export'])->name('export');
            Route::put('/{sanction}/statut', [CpeSanctionController::class, 'updateStatut'])->name('update-statut');
        });
        
        // Présences
        Route::prefix('presences')->name('presences.')->group(function () {
            Route::get('/', [CpePresenceController::class, 'index'])->name('index');
            Route::post('/marquer-presence', [CpePresenceController::class, 'marquerPresence'])->name('marquer-presence');
            Route::post('/marquer-absence', [CpePresenceController::class, 'marquerAbsence'])->name('marquer-absence');
            Route::post('/{absence}/justify', [CpePresenceController::class, 'justifierAbsence'])->name('justifier-absence');
            Route::get('/export', [CpePresenceController::class, 'export'])->name('export');
        });

        // Disciplines
        Route::prefix('disciplines')->name('disciplines.')->group(function () {
            Route::get('/', [CpeDisciplineController::class, 'index'])->name('index');
            Route::get('/create', [CpeDisciplineController::class, 'create'])->name('create');
            Route::post('/', [CpeDisciplineController::class, 'store'])->name('store');
            Route::get('/{discipline}', [CpeDisciplineController::class, 'show'])->name('show');
            Route::get('/{discipline}/edit', [CpeDisciplineController::class, 'edit'])->name('edit');
            Route::put('/{discipline}', [CpeDisciplineController::class, 'update'])->name('update');
            Route::delete('/{discipline}', [CpeDisciplineController::class, 'destroy'])->name('destroy');
            Route::get('/export', [CpeDisciplineController::class, 'export'])->name('export');
        });
        
        // Statistiques
        Route::get('/statistiques', [CpeDashboardController::class, 'statistiques'])->name('statistiques');
    });
    
    // ============================================
    // COMPTABLE
    // ============================================
    Route::middleware(['auth', 'role:comptable'])->prefix('comptable')->name('comptable.')->group(function () {
        Route::get('/dashboard', [ComptableDashboardController::class, 'index'])->name('dashboard');
        
        // Paiements
        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
        Route::get('/paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
        Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');
        Route::get('/paiements/{id}', [PaiementController::class, 'show'])->name('paiements.show');
        Route::get('/paiements/{id}/edit', [PaiementController::class, 'edit'])->name('paiements.edit');
        Route::put('/paiements/{id}', [PaiementController::class, 'update'])->name('paiements.update');
        Route::delete('/paiements/{id}', [PaiementController::class, 'destroy'])->name('paiements.destroy');
        Route::get('/paiements/{id}/recu', [PaiementController::class, 'recu'])->name('paiements.recu');
        Route::get('/paiements/export', [PaiementController::class, 'export'])->name('paiements.export');
        
        // Impayés - Routes directes
        Route::get('/impayes', [ImpayeController::class, 'index'])->name('impayes.index');
        Route::get('/impayes/statistiques', [ImpayeController::class, 'statistiques'])->name('impayes.statistiques');
        Route::get('/impayes/export', [ImpayeController::class, 'export'])->name('impayes.export');
        Route::get('/impayes/{impaye}', [ImpayeController::class, 'show'])->name('impayes.show');
        Route::post('/impayes/{impaye}/relance', [ImpayeController::class, 'relance'])->name('impayes.relance');
        Route::post('/impayes/engagement/{inscription}', [ImpayeController::class, 'engagement'])->name('impayes.engagement');
        Route::post('/impayes/{impaye}/resoudre', [ImpayeController::class, 'resoudre'])->name('impayes.resoudre');
        
        // Frais
        Route::get('/frais', [FraisScolariteController::class, 'index'])->name('frais.index');
        Route::get('/frais/create', [FraisScolariteController::class, 'create'])->name('frais.create');
        Route::post('/frais', [FraisScolariteController::class, 'store'])->name('frais.store');
        Route::get('/frais/{id}', [FraisScolariteController::class, 'show'])->name('frais.show');
        Route::get('/frais/{id}/edit', [FraisScolariteController::class, 'edit'])->name('frais.edit');
        Route::put('/frais/{id}', [FraisScolariteController::class, 'update'])->name('frais.update');
        Route::delete('/frais/{id}', [FraisScolariteController::class, 'destroy'])->name('frais.destroy');
        
        // Factures
        Route::get('/factures', [FactureController::class, 'index'])->name('factures.index');
        Route::get('/factures/create', [FactureController::class, 'create'])->name('factures.create');
        Route::post('/factures', [FactureController::class, 'store'])->name('factures.store');
        Route::get('/factures/{id}', [FactureController::class, 'show'])->name('factures.show');
        Route::get('/factures/{id}/edit', [FactureController::class, 'edit'])->name('factures.edit');
        Route::put('/factures/{id}', [FactureController::class, 'update'])->name('factures.update');
        Route::delete('/factures/{id}', [FactureController::class, 'destroy'])->name('factures.destroy');
        Route::post('/factures/{id}/payee', [FactureController::class, 'marquerPayee'])->name('factures.payee');
        Route::post('/factures/{id}/email', [FactureController::class, 'envoyerEmail'])->name('factures.email');
        Route::get('/factures/{id}/pdf', [FactureController::class, 'pdf'])->name('factures.pdf');
        
        // Dépenses
        Route::get('/depenses', [DepenseController::class, 'index'])->name('depenses.index');
        Route::get('/depenses/create', [DepenseController::class, 'create'])->name('depenses.create');
        Route::post('/depenses', [DepenseController::class, 'store'])->name('depenses.store');
        Route::get('/depenses/{id}', [DepenseController::class, 'show'])->name('depenses.show');
        Route::get('/depenses/{id}/edit', [DepenseController::class, 'edit'])->name('depenses.edit');
        Route::put('/depenses/{id}', [DepenseController::class, 'update'])->name('depenses.update');
        Route::delete('/depenses/{id}', [DepenseController::class, 'destroy'])->name('depenses.destroy');
        Route::get('/depenses/export', [DepenseController::class, 'export'])->name('depenses.export');
        
        // Rapports financiers
        Route::prefix('rapports')->name('rapports.')->group(function () {
            Route::get('/', [RapportFinancierController::class, 'index'])->name('index');
            Route::get('/journalier', [RapportFinancierController::class, 'journalier'])->name('journalier');
            Route::get('/mensuel', [RapportFinancierController::class, 'mensuel'])->name('mensuel');
            Route::get('/annuel', [RapportFinancierController::class, 'annuel'])->name('annuel');
            Route::get('/export', [RapportFinancierController::class, 'export'])->name('export');
        });
        
        // Export général
        Route::get('/export', [ComptableDashboardController::class, 'export'])->name('export');
    });
    
    // ============================================
    // ENSEIGNANT
    // ============================================
    Route::middleware(['role:enseignant'])->prefix('enseignant')->name('enseignant.')->group(function () {
        Route::get('/dashboard', [EnseignantDashboardController::class, 'index'])->name('dashboard');
        Route::get('/mes-classes', [EnseignantClasseController::class, 'index'])->name('classes');
        Route::get('/notes', [EnseignantNoteController::class, 'index'])->name('notes');
        Route::get('/presences', [EnseignantPresenceController::class, 'index'])->name('presences');
        Route::get('/emploi-du-temps', [EnseignantEmploiTempsController::class, 'index'])->name('emploi_temps');
        
        // Matières - Version avec alias
        Route::prefix('matieres')->name('matieres.')->group(function () {
            Route::get('/', [EnseignantMatiereController::class, 'index'])->name('index');
            Route::get('/{id}', [EnseignantMatiereController::class, 'show'])->name('show');
        });
        // Alias pour la route index (pour compatibilité)
        Route::get('/matieres', [EnseignantMatiereController::class, 'index'])->name('matieres');
        
        Route::get('/evaluations', [EnseignantEvaluationController::class, 'index'])->name('evaluations');
        
        // Rapports et statistiques
        Route::prefix('rapports')->name('rapports.')->group(function () {
        Route::get('/', [EnseignantRapportController::class, 'index'])->name('index');
        Route::get('/export', [EnseignantRapportController::class, 'export'])->name('export'); 
        });
        
        // Classes
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [EnseignantClasseController::class, 'index'])->name('index');
            Route::get('/{id}', [EnseignantClasseController::class, 'show'])->name('show');
            Route::get('/{id}/eleves', [EnseignantClasseController::class, 'eleves'])->name('eleves');
            Route::get('/{id}/export', [EnseignantClasseController::class, 'export'])->name('export');
        });

        // Gestion des notes
        Route::get('/notes/create/{classe}/{matiere}', [EnseignantNoteController::class, 'create'])->name('notes.create');
        Route::post('/notes', [EnseignantNoteController::class, 'store'])->name('notes.store');
        Route::get('/notes/{note}/edit', [EnseignantNoteController::class, 'edit'])->name('notes.edit');
        Route::put('/notes/{note}', [EnseignantNoteController::class, 'update'])->name('notes.update');
        Route::delete('/notes/{note}', [EnseignantNoteController::class, 'destroy'])->name('notes.destroy');
        Route::get('/notes/export', [EnseignantNoteController::class, 'export'])->name('notes.export');
        
        // Gestion des présences
        Route::post('/presences/marquer', [EnseignantPresenceController::class, 'marquer'])->name('presences.marquer');
        Route::get('/presences/export/{classe}', [EnseignantPresenceController::class, 'export'])->name('presences.export');
        
        // Emploi du temps
        Route::prefix('emploi-du-temps')->name('emploi_temps.')->group(function () {
            Route::get('/', [EnseignantEmploiTempsController::class, 'index'])->name('index');
            Route::get('/create', [EnseignantEmploiTempsController::class, 'create'])->name('create');
            Route::post('/', [EnseignantEmploiTempsController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [EnseignantEmploiTempsController::class, 'edit'])->name('edit');
            Route::put('/{id}', [EnseignantEmploiTempsController::class, 'update'])->name('update');
            Route::delete('/{id}', [EnseignantEmploiTempsController::class, 'destroy'])->name('destroy');
            Route::get('/semaine', [EnseignantEmploiTempsController::class, 'semaine'])->name('semaine');
            Route::get('/jour/{date}', [EnseignantEmploiTempsController::class, 'jour'])->name('jour');
        });
    });
    
    // ============================================
    // PARENT
    // ============================================
    Route::middleware(['role:parent'])->prefix('parent')->name('parent.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
        
        // Enfants
        Route::prefix('enfants')->name('enfants.')->group(function () {
            Route::get('/', [EnfantController::class, 'index'])->name('index');
            Route::get('/{id}', [EnfantController::class, 'show'])->name('show');
        });
        
        // Notes
        Route::prefix('notes')->name('notes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Parent\NoteController::class, 'index'])->name('index');
            Route::get('/enfant/{id}', [\App\Http\Controllers\Parent\NoteController::class, 'enfant'])->name('enfant');
            Route::get('/{id}', [\App\Http\Controllers\Parent\NoteController::class, 'show'])->name('show');
        });
        
        // Absences
        Route::prefix('absences')->name('absences.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Parent\AbsenceController::class, 'index'])->name('index');
            Route::get('/enfant/{id}', [\App\Http\Controllers\Parent\AbsenceController::class, 'enfant'])->name('enfant');
            Route::post('/{id}/justify', [\App\Http\Controllers\Parent\AbsenceController::class, 'justify'])->name('justify');
        });
            
        // Emploi du temps
        Route::prefix('emploi-temps')->name('emploi_temps.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Parent\EmploiTempsController::class, 'index'])->name('index');
            Route::get('/enfant/{id}', [\App\Http\Controllers\Parent\EmploiTempsController::class, 'enfant'])->name('enfant');
            Route::get('/enfant/{id}/semaine', [\App\Http\Controllers\Parent\EmploiTempsController::class, 'semaine'])->name('semaine');
        });
        
        // Bulletins
        Route::prefix('bulletins')->name('bulletins.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Parent\BulletinController::class, 'index'])->name('index');
            Route::get('/enfant/{id}', [\App\Http\Controllers\Parent\BulletinController::class, 'enfant'])->name('enfant');
            Route::get('/pdf/{id}', [\App\Http\Controllers\Parent\BulletinController::class, 'pdf'])->name('pdf');
        });
        
        // Paiements
        Route::prefix('paiements')->name('paiements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Parent\PaiementController::class, 'index'])->name('index');
            Route::get('/enfant/{id}', [\App\Http\Controllers\Parent\PaiementController::class, 'enfant'])->name('enfant');
            Route::get('/recu/{id}', [\App\Http\Controllers\Parent\PaiementController::class, 'recu'])->name('recu');
        });
        
        // Communications
        Route::prefix('communications')->name('communications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Parent\CommunicationController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Parent\CommunicationController::class, 'create'])->name('create');
            Route::post('/send', [\App\Http\Controllers\Parent\CommunicationController::class, 'send'])->name('send');
            Route::get('/sent', [\App\Http\Controllers\Parent\CommunicationController::class, 'sent'])->name('sent');
            Route::get('/{id}', [\App\Http\Controllers\Parent\CommunicationController::class, 'show'])->name('show');
            Route::post('/{id}/reply', [\App\Http\Controllers\Parent\CommunicationController::class, 'reply'])->name('reply');
            Route::delete('/{id}', [\App\Http\Controllers\Parent\CommunicationController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/mark-read', [\App\Http\Controllers\Parent\CommunicationController::class, 'markAsRead'])->name('mark-read');
        });
    });
    
    // ============================================
    // ÉLÈVE
    // ============================================
    Route::middleware(['role:eleve'])->prefix('eleve')->name('eleve.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [EleveDashboardController::class, 'index'])->name('dashboard');
        
        // Notes
        Route::get('/mes-notes', [EleveNoteController::class, 'index'])->name('notes');
        Route::get('/note/{id}', [EleveNoteController::class, 'show'])->name('note.show');
        
        // Emploi du temps
        Route::get('/emploi-temps', [EleveEmploiTempsController::class, 'index'])->name('emploi_temps');
        
        // Absences
        Route::get('/absences', [EleveAbsenceController::class, 'index'])->name('absences');
        Route::get('/absence/{id}', [EleveAbsenceController::class, 'show'])->name('absence.show');
        
        // Bulletins
        Route::get('/bulletins', [EleveBulletinController::class, 'index'])->name('bulletins');
        Route::get('/bulletin/{trimestre}', [EleveBulletinController::class, 'show'])->name('bulletin.show');
        
        // ============================================
        // PROFIL ÉLÈVE (avec ProfilController)
        // ============================================
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\Eleve\ProfilController::class, 'index'])->name('index');
            Route::get('/edit', [App\Http\Controllers\Eleve\ProfilController::class, 'edit'])->name('edit');
            Route::put('/', [App\Http\Controllers\Eleve\ProfilController::class, 'update'])->name('update');
            Route::put('/password', [App\Http\Controllers\Eleve\ProfilController::class, 'updatePassword'])->name('password');
            Route::post('/photo', [App\Http\Controllers\Eleve\ProfilController::class, 'updatePhoto'])->name('photo');
        });
    });
});

// Routes accessibles avec plusieurs rôles
Route::middleware(['auth', 'manager'])->prefix('gestion')->name('gestion.')->group(function () {
    Route::get('/statistiques', [App\Http\Controllers\Gestion\StatistiqueController::class, 'index'])->name('statistiques');
    Route::get('/export', [App\Http\Controllers\Gestion\ExportController::class, 'index'])->name('export');
});

// Fallback route pour les erreurs 404
Route::fallback(function () {
    return view('errors.404');
});