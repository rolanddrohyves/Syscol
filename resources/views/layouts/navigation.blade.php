{{-- resources/views/layouts/navigation.blade.php --}}
@php
    $role = Auth::user()->role->name ?? '';
    $currentRoute = request()->route()->getName();
    $etablissementId = Auth::user()->etablissement_id;
    $userId = Auth::id();
    $enseignantId = App\Models\Enseignant::where('user_id', $userId)->value('user_id') ?? $userId;
    
    // Statistiques pour CPE et Admin
    $absencesAujourdhui = 0;
    $retardsAujourdhui = 0;
    $sanctionsEnCours = 0;
    
    if(in_array($role, ['cpe', 'admin_etablissement'])) {
        try {
            $absencesAujourdhui = App\Models\Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->whereDate('date', today())->count();
        } catch (\Exception $e) { $absencesAujourdhui = 0; }
        
        try {
            $retardsAujourdhui = App\Models\Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('type', 'retard')->whereDate('date', today())->count();
        } catch (\Exception $e) { $retardsAujourdhui = 0; }
        
        try {
            $sanctionsEnCours = App\Models\Sanction::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
                ->where('statut', 'en_cours')->count();
        } catch (\Exception $e) { $sanctionsEnCours = 0; }
    }
    
    // Statistiques pour enseignant
    $mesClasses = 0;
    $coursAujourdhui = 0;
    $notesASaisir = 0;
    $totalNotes = 0;
    $totalMatieres = 0;
    $premiereClasse = null;
    $premiereMatiere = null;
    
    if($role == 'enseignant') {
        $enseignantRecord = App\Models\Enseignant::where('user_id', $userId)->first();
        $enseignantIdDb = $enseignantRecord ? $enseignantRecord->user_id : $userId;
        $mesClasses = App\Models\EmploiTemps::where('enseignant_id', $enseignantIdDb)->distinct('classe_id')->count('classe_id');
        $coursAujourdhui = App\Models\EmploiTemps::where('enseignant_id', $enseignantIdDb)
            ->where('jour', now()->locale('fr')->dayName)->count();
        $notesASaisir = App\Models\Note::where('enseignant_id', $enseignantIdDb)->whereNull('note')->count();
        $totalNotes = App\Models\Note::where('enseignant_id', $enseignantIdDb)->count();
        $totalMatieres = App\Models\Enseignant::find($enseignantIdDb)?->matieres->count() ?? 0;
        $premiereClasse = App\Models\EmploiTemps::where('enseignant_id', $enseignantIdDb)->first();
        $premiereMatiere = App\Models\Enseignant::find($enseignantIdDb)?->matieres->first();
    }
    
    // Statistiques pour parent
    $totalEnfants = 0;
    $totalAbsencesParent = 0;
    $totalNotesParent = 0;
    $totalPaiements = 0;
    $totalResteAPayer = 0;
    $absencesNonJustifiees = 0;
    $enfantsList = collect();
    
    if($role == 'parent') {
        $parentUser = Auth::user();
        $enfantsList = App\Models\Eleve::where('email_parent', $parentUser->email)
            ->orWhere('telephone_parent', $parentUser->telephone)
            ->get();
        
        $totalEnfants = $enfantsList->count();
        
        if($totalEnfants > 0) {
            $totalAbsencesParent = App\Models\Absence::whereIn('eleve_id', $enfantsList->pluck('id'))->count();
            $totalNotesParent = App\Models\Note::whereIn('eleve_id', $enfantsList->pluck('id'))->count();
            
            foreach($enfantsList as $enfant) {
                $totalPaiements += $enfant->montant_paye ?? 0;
                $totalResteAPayer += $enfant->montant_restant ?? 0;
            }
            
            $absencesNonJustifiees = App\Models\Absence::whereIn('eleve_id', $enfantsList->pluck('id'))
                ->where('justifiee', false)
                ->whereDate('date', '>=', now()->subDays(7))
                ->count();
        }
    }
    
    // Statistiques pour comptable
    $paiementsMois = 0;
    $impayesCount = 0;
    $depensesMois = 0;
    $facturesImpayees = 0;
    
    if($role == 'comptable') {
        $paiementsMois = App\Models\Paiement::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
            ->whereMonth('date_paiement', now()->month)->sum('montant') ?? 0;
        $impayesCount = App\Models\Paiement::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))
            ->whereIn('statut', ['en_attente', 'partiel'])->count();
        $depensesMois = App\Models\Depense::where('etablissement_id', $etablissementId)
            ->whereMonth('date', now()->month)->sum('montant') ?? 0;
        $facturesImpayees = App\Models\Facture::where('etablissement_id', $etablissementId)
            ->where('statut', 'impayee')->count();
    }
    
    // ============================================
    // STATISTIQUES POUR LES MESSAGES NON LUS
    // ============================================
    $messagesNonLus = 0;
    $messagesNonLusParent = 0;
    
    if($role == 'super_admin') {
        $messagesNonLus = App\Models\Message::where('receiver_id', Auth::id())
            ->where('type', 'admin_to_superadmin')
            ->where('lu', false)
            ->count();
    }
    
    if($role == 'admin_etablissement') {
        $messagesNonLus = App\Models\Message::where('receiver_id', Auth::id())
            ->where('type', 'parent_to_admin')
            ->where('lu', false)
            ->count();
    }
    
    if($role == 'parent') {
        $messagesNonLusParent = App\Models\Message::where('receiver_id', Auth::id())
            ->where('type', 'admin_reply')
            ->where('lu', false)
            ->count();
    }
@endphp

<nav class="space-y-1 px-2">

    @if($role == 'super_admin')
        <!-- SUPER ADMIN -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Administration</p>
        </div>
        
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'admin.dashboard' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-3">Tableau de bord</span>
            </div>
        </a>
        
        <a href="{{ route('admin.etablissements') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'admin.etablissements') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-school w-5"></i>
                <span class="ml-3">Établissements</span>
            </div>
            <span class="bg-indigo-600 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\Etablissement::count() }}</span>
        </a>
        
        <!-- Menu Utilisateurs -->
        <div x-data="{ open: {{ str_contains($currentRoute, 'admin.users') || str_contains($currentRoute, 'admin.roles') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Utilisateurs</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-indigo-600 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\User::count() }}</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('admin.users') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'admin.users' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Liste des utilisateurs</span>
                    <span class="text-xs">{{ App\Models\User::count() }}</span>
                </a>
                <a href="{{ route('admin.users.create') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'admin.users.create' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <i class="fas fa-plus w-4 mr-3"></i>Ajouter
                </a>
                <a href="{{ route('admin.roles') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'admin.roles' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-user-tag w-4 mr-3"></i>Rôles</span>
                    <span class="text-xs">{{ App\Models\Role::count() }}</span>
                </a>
            </div>
        </div>

        <!-- Menu Configuration -->
        <div x-data="{ open: {{ str_contains($currentRoute, 'admin.configurations') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Configuration</span>
                </div>
                <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('admin.configurations.general') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'admin.configurations.general' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <i class="fas fa-sliders-h w-4 mr-3"></i>Général
                </a>
                <a href="{{ route('admin.configurations.auth') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'admin.configurations.auth' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <i class="fas fa-lock w-4 mr-3"></i>Authentification
                </a>
                <a href="{{ route('admin.configurations.modules') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'admin.configurations.modules' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <i class="fas fa-puzzle-piece w-4 mr-3"></i>Modules
                </a>
                <a href="{{ route('admin.configurations.mail') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'admin.configurations.mail' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <i class="fas fa-envelope w-4 mr-3"></i>Mail
                </a>
            </div>
        </div>
        
        <a href="{{ route('admin.statistiques') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'admin.statistiques') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-line w-5"></i>
                <span class="ml-3">Statistiques</span>
            </div>
        </a>
        
        <a href="{{ route('admin.logs.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'admin.logs') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-history w-5"></i>
                <span class="ml-3">Journaux</span>
            </div>
        </a>

        <!-- COMMUNICATIONS - SUPER ADMIN -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Communication</p>
        </div>

        <a href="{{ route('admin.communications.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'admin.communications') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-envelope w-5"></i>
                <span class="ml-3">Messagerie</span>
            </div>
            @if($messagesNonLus > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $messagesNonLus }}</span>
            @else
                <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full">0</span>
            @endif
        </a>

    @elseif($role == 'admin_etablissement')
        <!-- ADMIN ÉTABLISSEMENT -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Gestion</p>
        </div>

        <a href="{{ route('etablissement.dashboard', $etablissementId) }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.dashboard') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-3">Tableau de bord</span>
            </div>
        </a>

        <a href="{{ route('etablissement.classes.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.classes') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-door-open w-5"></i>
                <span class="ml-3">Classes</span>
            </div>
            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\Classe::where('etablissement_id', $etablissementId)->count() }}</span>
        </a>

        <a href="{{ route('etablissement.eleves.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.eleves') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-user-graduate w-5"></i>
                <span class="ml-3">Élèves</span>
            </div>
            <div class="flex items-center space-x-2">
                @php
                    $nouveauxEleves = App\Models\Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))->whereDate('created_at', today())->count();
                @endphp
                @if($nouveauxEleves > 0)
                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">+{{ $nouveauxEleves }}</span>
                @endif
                <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count() }}</span>
            </div>
        </a>

        <a href="{{ route('etablissement.enseignants.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.enseignants') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chalkboard-teacher w-5"></i>
                <span class="ml-3">Enseignants</span>
            </div>
            <span class="bg-purple-600 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\Enseignant::whereHas('user', fn($q) => $q->where('etablissement_id', $etablissementId))->count() }}</span>
        </a>

        <a href="{{ route('etablissement.matieres.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.matieres') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-book-open w-5"></i>
                <span class="ml-3">Matières</span>
            </div>
            <span class="bg-amber-600 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\Matiere::count() }}</span>
        </a>

        <a href="{{ route('etablissement.absences.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.absences') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-calendar-times w-5"></i>
                <span class="ml-3">Absences</span>
            </div>
            @if($absencesAujourdhui > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $absencesAujourdhui }} aujourd'hui</span>
            @else
                <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\Absence::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count() }}</span>
            @endif
        </a>

        <a href="{{ route('etablissement.emplois_temps.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.emplois_temps') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt w-5"></i>
                <span class="ml-3">Emplois du temps</span>
            </div>
        </a>

        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Administration</p>
        </div>

        <a href="{{ route('etablissement.annes_scolaires.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.annes_scolaires') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-calendar w-5"></i>
                <span class="ml-3">Années scolaires</span>
            </div>
        </a>

        <a href="{{ route('etablissement.utilisateurs.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.utilisateurs') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-users-cog w-5"></i>
                <span class="ml-3">Personnel</span>
            </div>
        </a>

        <a href="{{ route('etablissement.parametres.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.parametres') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-sliders-h w-5"></i>
                <span class="ml-3">Paramètres</span>
            </div>
        </a>

        <!-- COMMUNICATIONS - ADMIN ÉTABLISSEMENT -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Communication</p>
        </div>

        <a href="{{ route('etablissement.communications.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'etablissement.communications') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-envelope w-5"></i>
                <span class="ml-3">Messagerie</span>
            </div>
            @if($messagesNonLus > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $messagesNonLus }}</span>
            @else
                <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full">0</span>
            @endif
        </a>

    @elseif($role == 'cpe')
        <!-- CPE -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Suivi</p>
        </div>

        <a href="{{ route('cpe.dashboard') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'cpe.dashboard' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-3">Tableau de bord</span>
            </div>
        </a>

        <a href="{{ route('cpe.absences.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'cpe.absences') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-calendar-times w-5"></i>
                <span class="ml-3">Absences</span>
            </div>
            @if($absencesAujourdhui > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $absencesAujourdhui }}</span>
            @else
                <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full">0</span>
            @endif
        </a>

        <a href="{{ route('cpe.sanctions.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'cpe.sanctions') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-gavel w-5"></i>
                <span class="ml-3">Sanctions</span>
            </div>
            @if($sanctionsEnCours > 0)
                <span class="bg-purple-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $sanctionsEnCours }} en cours</span>
            @else
                <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full">{{ App\Models\Sanction::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count() }}</span>
            @endif
        </a>

        <a href="{{ route('cpe.presences.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'cpe.presences') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-check-circle w-5"></i>
                <span class="ml-3">Présences</span>
            </div>
            @php
                $totalEleves = App\Models\Eleve::whereHas('classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count();
                $tauxPresence = $totalEleves > 0 ? round((($totalEleves - $absencesAujourdhui) / $totalEleves) * 100) : 0;
            @endphp
            <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full">{{ $tauxPresence }}%</span>
        </a>

    @elseif($role == 'enseignant')
        <!-- ENSEIGNANT -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Mon enseignement</p>
        </div>

        <a href="{{ route('enseignant.dashboard') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'enseignant.dashboard' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-line w-5"></i>
                <span class="ml-3">Tableau de bord</span>
            </div>
            @if($coursAujourdhui > 0)
                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $coursAujourdhui }} cours</span>
            @endif
        </a>

        <a href="{{ route('enseignant.classes') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'enseignant.classes' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-door-open w-5"></i>
                <span class="ml-3">Mes classes</span>
            </div>
            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">{{ $mesClasses }}</span>
        </a>

        <a href="{{ route('enseignant.matieres') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'enseignant.matieres' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-book-open w-5"></i>
                <span class="ml-3">Mes matières</span>
            </div>
            <span class="bg-purple-600 text-white text-xs px-2 py-1 rounded-full">{{ $totalMatieres }}</span>
        </a>

        <div x-data="{ open: {{ str_contains($currentRoute, 'enseignant.notes') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-star w-5"></i>
                    <span class="ml-3">Notes</span>
                </div>
                <div class="flex items-center space-x-2">
                    @if($notesASaisir > 0)
                        <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $notesASaisir }} à saisir</span>
                    @endif
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('enseignant.notes') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'enseignant.notes' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Toutes les notes</span>
                    <span class="text-xs">{{ $totalNotes }}</span>
                </a>
                @if($premiereClasse && $premiereMatiere)
                <a href="{{ route('enseignant.notes.create', ['classe' => $premiereClasse->classe_id, 'matiere' => $premiereMatiere->id]) }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 text-white/60 hover:bg-indigo-800/30 hover:text-white">
                    <i class="fas fa-plus w-4 mr-3"></i>Saisir des notes
                </a>
                @endif
            </div>
        </div>

        <a href="{{ route('enseignant.presences') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'enseignant.presences' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-check-circle w-5"></i>
                <span class="ml-3">Présences / Appel</span>
            </div>
            @if($coursAujourdhui > 0)
                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $coursAujourdhui }} cours</span>
            @else
                <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full">0</span>
            @endif
        </a>

        <a href="{{ route('enseignant.emploi_temps.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'enseignant.emploi_temps') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt w-5"></i>
                <span class="ml-3">Emploi du temps</span>
            </div>
            <span class="bg-cyan-600 text-white text-xs px-2 py-1 rounded-full">{{ $coursAujourdhui }} aujourd'hui</span>
        </a>

        <a href="{{ route('enseignant.rapports.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'enseignant.rapports' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-bar w-5"></i>
                <span class="ml-3">Rapports</span>
            </div>
        </a>

    @elseif($role == 'comptable')
        <!-- COMPTABLE -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Gestion financière</p>
        </div>

        <a href="{{ route('comptable.dashboard') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'comptable.dashboard') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-3">Tableau de bord</span>
            </div>
            @if($impayesCount > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $impayesCount }}</span>
            @endif
        </a>

        <!-- Paiements -->
        <div x-data="{ open: {{ str_contains($currentRoute, 'comptable.paiements') || str_contains($currentRoute, 'comptable.impayes') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave w-5"></i>
                    <span class="ml-3">Paiements</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full">{{ number_format($paiementsMois, 0, ',', ' ') }} FCFA</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('comptable.paiements.index') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'comptable.paiements.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Tous les paiements</span>
                    <span class="text-xs">{{ App\Models\Paiement::whereHas('eleve.classe', fn($q) => $q->where('etablissement_id', $etablissementId))->count() }}</span>
                </a>
                <a href="{{ route('comptable.impayes.index') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'comptable.impayes.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-exclamation-triangle w-4 mr-3"></i>Impayés</span>
                    @if($impayesCount > 0)
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $impayesCount }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Frais de scolarité -->
        <div x-data="{ open: {{ str_contains($currentRoute, 'comptable.frais') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-tag w-5"></i>
                    <span class="ml-3">Frais de scolarité</span>
                </div>
                <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('comptable.frais.index') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'comptable.frais.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Liste des frais</span>
                </a>
                <a href="{{ route('comptable.frais.create') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'comptable.frais.create' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <i class="fas fa-plus w-4 mr-3"></i>Nouveau frais
                </a>
            </div>
        </div>

        <!-- Factures -->
        <div x-data="{ open: {{ str_contains($currentRoute, 'comptable.factures') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-file-invoice w-5"></i>
                    <span class="ml-3">Factures</span>
                </div>
                <div class="flex items-center space-x-2">
                    @if($facturesImpayees > 0)
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $facturesImpayees }}</span>
                    @endif
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('comptable.factures.index') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'comptable.factures.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Toutes les factures</span>
                    <span class="text-xs">{{ App\Models\Facture::where('etablissement_id', $etablissementId)->count() }}</span>
                </a>
                <a href="{{ route('comptable.factures.create') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'comptable.factures.create' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <i class="fas fa-plus w-4 mr-3"></i>Nouvelle facture
                </a>
            </div>
        </div>

        <!-- Dépenses -->
        <div x-data="{ open: {{ str_contains($currentRoute, 'comptable.depenses') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Dépenses</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-red-600 text-white text-xs px-2 py-1 rounded-full">{{ number_format($depensesMois, 0, ',', ' ') }} FCFA</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('comptable.depenses.index') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'comptable.depenses.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Liste des dépenses</span>
                    <span class="text-xs">{{ App\Models\Depense::where('etablissement_id', $etablissementId)->count() }}</span>
                </a>
                <a href="{{ route('comptable.depenses.create') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'comptable.depenses.create' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <i class="fas fa-plus w-4 mr-3"></i>Nouvelle dépense
                </a>
            </div>
        </div>

        <!-- Rapports financiers -->
        <a href="{{ route('comptable.rapports.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'comptable.rapports') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-bar w-5"></i>
                <span class="ml-3">Rapports</span>
            </div>
        </a>

    @elseif($role == 'parent')
        <!-- PARENT -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Mon espace</p>
        </div>

        <a href="{{ route('parent.dashboard') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'parent.dashboard' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-3">Tableau de bord</span>
            </div>
            @if($absencesNonJustifiees > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $absencesNonJustifiees }}</span>
            @endif
        </a>

        <a href="{{ route('parent.enfants.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'parent.enfants.index' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-users w-5"></i>
                <span class="ml-3">Mes enfants</span>
            </div>
            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">{{ $totalEnfants }}</span>
        </a>

        <div x-data="{ open: {{ str_contains($currentRoute, 'parent.notes') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-star w-5"></i>
                    <span class="ml-3">Notes</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-purple-600 text-white text-xs px-2 py-1 rounded-full">{{ $totalNotesParent }}</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('parent.notes.index') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'parent.notes.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Toutes les notes</span>
                    <span class="text-xs">{{ $totalNotesParent }}</span>
                </a>
                @foreach($enfantsList->take(3) as $enfant)
                <a href="{{ route('parent.notes.enfant', $enfant->id) }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 text-white/60 hover:bg-indigo-800/30 hover:text-white">
                    <i class="fas fa-child w-4 mr-3"></i>{{ $enfant->prenom }} {{ $enfant->nom }}
                </a>
                @endforeach
            </div>
        </div>

        <div x-data="{ open: {{ str_contains($currentRoute, 'parent.absences') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-calendar-times w-5"></i>
                    <span class="ml-3">Absences</span>
                </div>
                <div class="flex items-center space-x-2">
                    @if($absencesNonJustifiees > 0)
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $absencesNonJustifiees }}</span>
                    @else
                        <span class="bg-orange-600 text-white text-xs px-2 py-1 rounded-full">{{ $totalAbsencesParent }}</span>
                    @endif
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('parent.absences.index') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'parent.absences.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Toutes les absences</span>
                    <span class="text-xs">{{ $totalAbsencesParent }}</span>
                </a>
                @foreach($enfantsList->take(3) as $enfant)
                <a href="{{ route('parent.absences.enfant', $enfant->id) }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 text-white/60 hover:bg-indigo-800/30 hover:text-white">
                    <i class="fas fa-child w-4 mr-3"></i>{{ $enfant->prenom }} {{ $enfant->nom }}
                </a>
                @endforeach
            </div>
        </div>

        <div x-data="{ open: {{ str_contains($currentRoute, 'parent.paiements') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-money-bill-wave w-5"></i>
                    <span class="ml-3">Paiements</span>
                </div>
                <div class="flex items-center space-x-2">
                    @if($totalResteAPayer > 0)
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ number_format($totalResteAPayer, 0, ',', ' ') }}</span>
                    @else
                        <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full">À jour</span>
                    @endif
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('parent.paiements.index') }}" 
                   class="flex items-center justify-between px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'parent.paiements.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Récapitulatif</span>
                    <span class="text-xs">{{ number_format($totalPaiements, 0, ',', ' ') }} FCFA</span>
                </a>
                @foreach($enfantsList->take(3) as $enfant)
                <a href="{{ route('parent.paiements.enfant', $enfant->id) }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 text-white/60 hover:bg-indigo-800/30 hover:text-white">
                    <i class="fas fa-child w-4 mr-3"></i>{{ $enfant->prenom }} {{ $enfant->nom }}
                </a>
                @endforeach
            </div>
        </div>

        <div x-data="{ open: {{ str_contains($currentRoute, 'parent.emploi_temps') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt w-5"></i>
                    <span class="ml-3">Emploi du temps</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-cyan-600 text-white text-xs px-2 py-1 rounded-full">{{ $totalEnfants }}</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </div>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('parent.emploi_temps.index') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'parent.emploi_temps.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-calendar-week w-4 mr-3"></i>Vue générale</span>
                </a>
                @foreach($enfantsList->take(3) as $enfant)
                <a href="{{ route('parent.emploi_temps.enfant', $enfant->id) }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 text-white/60 hover:bg-indigo-800/30 hover:text-white">
                    <i class="fas fa-child w-4 mr-3"></i>{{ $enfant->prenom }} {{ $enfant->nom }}
                </a>
                @endforeach
            </div>
        </div>

        <div x-data="{ open: {{ str_contains($currentRoute, 'parent.bulletins') ? 'true' : 'false' }} }">
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 text-white/70 hover:bg-indigo-800/50 hover:text-white">
                <div class="flex items-center">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="ml-3">Bulletins</span>
                </div>
                <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>
            
            <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('parent.bulletins.index') }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'parent.bulletins.index' ? 'bg-indigo-800 text-white' : 'text-white/60 hover:bg-indigo-800/30 hover:text-white' }}">
                    <span><i class="fas fa-list w-4 mr-3"></i>Tous les bulletins</span>
                </a>
                @foreach($enfantsList->take(3) as $enfant)
                <a href="{{ route('parent.bulletins.enfant', $enfant->id) }}" 
                   class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-300 text-white/60 hover:bg-indigo-800/30 hover:text-white">
                    <i class="fas fa-child w-4 mr-3"></i>{{ $enfant->prenom }} {{ $enfant->nom }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- COMMUNICATIONS - PARENT -->
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Communication</p>
        </div>

        <a href="{{ route('parent.communications.index') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ str_contains($currentRoute, 'parent.communications') ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-envelope w-5"></i>
                <span class="ml-3">Messagerie</span>
            </div>
            @if($messagesNonLusParent > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $messagesNonLusParent }}</span>
            @else
                <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full">0</span>
            @endif
        </a>

        <a href="{{ route('profile.show') }}" 
           class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'profile.show' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-user-circle w-5"></i>
                <span class="ml-3">Mon profil</span>
            </div>
        </a>

    @elseif($role == 'eleve')
        <!-- ÉLÈVE -->
        @php
            // Récupérer l'élève connecté
            $eleveConnecte = App\Models\Eleve::where('user_id', Auth::id())->first();
            $eleveId = $eleveConnecte ? $eleveConnecte->id : null;
            
            // Statistiques pour l'élève
            $totalNotes = 0;
            $absencesNonJustifiees = 0;
            $bulletinsCount = 0;
            
            if($eleveId) {
                $totalNotes = App\Models\Note::where('eleve_id', $eleveId)->count();
                $absencesNonJustifiees = App\Models\Absence::where('eleve_id', $eleveId)
                    ->where('justifiee', false)
                    ->count();
                $bulletinsCount = App\Models\Bulletin::where('eleve_id', $eleveId)->count();
            }
        @endphp
        
        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Mon espace</p>
        </div>

        <!-- Tableau de bord -->
        <a href="{{ route('eleve.dashboard') }}" 
        class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'eleve.dashboard' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-chart-pie w-5"></i>
                <span class="ml-3">Tableau de bord</span>
            </div>
        </a>

        <!-- Mes notes -->
        <a href="{{ route('eleve.notes') }}" 
        class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'eleve.notes' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-star w-5"></i>
                <span class="ml-3">Mes notes</span>
            </div>
            @if($totalNotes > 0)
                <span class="bg-purple-600 text-white text-xs px-2 py-1 rounded-full">{{ $totalNotes }}</span>
            @endif
        </a>

        <!-- Mon emploi du temps -->
        <a href="{{ route('eleve.emploi_temps') }}" 
        class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'eleve.emploi_temps' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt w-5"></i>
                <span class="ml-3">Mon emploi du temps</span>
            </div>
        </a>

        <!-- Mes absences -->
        <a href="{{ route('eleve.absences') }}" 
        class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'eleve.absences' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-calendar-times w-5"></i>
                <span class="ml-3">Mes absences</span>
            </div>
            @if($absencesNonJustifiees > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">{{ $absencesNonJustifiees }}</span>
            @endif
        </a>

        <!-- Mes bulletins -->
        <a href="{{ route('eleve.bulletins') }}" 
        class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'eleve.bulletins' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-file-alt w-5"></i>
                <span class="ml-3">Mes bulletins</span>
            </div>
            @if($bulletinsCount > 0)
                <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full">{{ $bulletinsCount }}</span>
            @endif
        </a>

        <div class="pt-4 pb-2">
            <p class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">Personnel</p>
        </div>

        <!-- Mon profil -->
        <a href="{{ route('eleve.profile.index') }}" 
        class="flex items-center justify-between px-4 py-3 text-sm rounded-lg transition-all duration-300 {{ $currentRoute == 'eleve.profile.index' || $currentRoute == 'eleve.profile.edit' ? 'bg-indigo-800 text-white shadow-lg' : 'text-white/70 hover:bg-indigo-800/50 hover:text-white hover:translate-x-1' }}">
            <div class="flex items-center">
                <i class="fas fa-user-circle w-5"></i>
                <span class="ml-3">Mon profil</span>
            </div>
        </a>
    @endif

    <!-- Déconnexion -->
    <div class="pt-6 mt-6 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-white/70 hover:bg-red-600/20 hover:text-red-300 rounded-lg transition-all duration-300 group hover:translate-x-1">
                <i class="fas fa-sign-out-alt w-5 group-hover:scale-110 transition-transform"></i>
                <span class="ml-3">Déconnexion</span>
            </button>
        </form>
    </div>
</nav>

<style>
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    [x-cloak] { display: none !important; }
</style>