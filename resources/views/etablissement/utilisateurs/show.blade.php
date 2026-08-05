{{-- resources/views/etablissement/utilisateurs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails utilisateur - SYSCOL')
@section('page-title', 'Détails de l\'utilisateur')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-circle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $utilisateur->name }}</h2>
                <p class="text-sm text-gray-500">{{ $utilisateur->role->display_name ?? $utilisateur->role->name }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('etablissement.utilisateurs.edit', $utilisateur->id) }}" 
               class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            
            <a href="{{ route('etablissement.utilisateurs.index') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Photo et infos de base -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex flex-col items-center">
                    <!-- Avatar -->
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white text-5xl font-bold mb-4 shadow-lg">
                        {{ substr($utilisateur->name, 0, 1) }}
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800">{{ $utilisateur->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $utilisateur->email }}</p>
                    
                    <!-- Statut -->
                    <div class="mt-4">
                        @if($utilisateur->is_active)
                            <span class="px-4 py-2 text-sm rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-circle text-xs mr-2"></i>
                                Compte actif
                            </span>
                        @else
                            <span class="px-4 py-2 text-sm rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-circle text-xs mr-2"></i>
                                Compte inactif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-address-card text-purple-600 mr-2"></i>
                        Contact
                    </h4>
                    
                    <dl class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Email</dt>
                            <dd class="font-medium text-gray-900">{{ $utilisateur->email }}</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Téléphone</dt>
                            <dd class="font-medium text-gray-900">{{ $utilisateur->telephone ?? 'Non renseigné' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Informations détaillées -->
        <div class="lg:col-span-2">
            <!-- Rôle et permissions -->
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-shield-alt text-purple-600 mr-2"></i>
                    Rôle et permissions
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Rôle actuel</p>
                        <div class="p-4 bg-purple-50 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-purple-200 flex items-center justify-center mr-3">
                                    <i class="fas fa-{{ $utilisateur->role->name == 'admin_etablissement' ? 'crown' : ($utilisateur->role->name == 'enseignant' ? 'chalkboard-teacher' : 'user-tie') }} text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $utilisateur->role->display_name ?? $utilisateur->role->name }}</p>
                                    <p class="text-xs text-gray-500">Niveau {{ $utilisateur->role->level ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Permissions principales</p>
                        <div class="space-y-2">
                            @php
                                $permissions = [
                                    'admin_etablissement' => ['Gérer établissement', 'Gérer utilisateurs', 'Gérer classes', 'Gérer notes'],
                                    'directeur_etudes' => ['Gérer emplois du temps', 'Gérer examens', 'Gérer bulletins'],
                                    'cpe' => ['Gérer absences', 'Gérer retards', 'Gérer disciplines'],
                                    'comptable' => ['Gérer frais', 'Gérer paiements', 'Gérer factures'],
                                    'enseignant' => ['Gérer notes', 'Gérer présences', 'Consulter emploi du temps'],
                                ];
                                $userPermissions = $permissions[$utilisateur->role->name] ?? ['Accès standard'];
                            @endphp
                            
                            @foreach($userPermissions as $permission)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-check-circle text-green-500 mr-2 text-xs"></i>
                                    {{ $permission }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-2"></i>
                    Activité récente
                </h4>
                
                <div class="space-y-4">
                    <!-- Dernière connexion -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-sign-in-alt text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Dernière connexion</p>
                                <p class="text-xs text-gray-500">
                                    @if($utilisateur->last_login_at)
                                        {{ $utilisateur->last_login_at->format('d/m/Y à H:i') }}
                                    @else
                                        Jamais connecté
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Dernière action -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Dernière action</p>
                                <p class="text-xs text-gray-500">
                                    @if($utilisateur->updated_at)
                                        {{ $utilisateur->updated_at->format('d/m/Y à H:i') }}
                                    @else
                                        Aucune action
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Compte créé le -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-plus text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Compte créé le</p>
                                <p class="text-xs text-gray-500">
                                    {{ $utilisateur->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Si c'est un enseignant, afficher les infos supplémentaires -->
    @if($utilisateur->role->name == 'enseignant' && $utilisateur->enseignant)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chalkboard-teacher text-purple-600 mr-2"></i>
            Informations enseignant
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Matricule</p>
                <p class="font-medium text-gray-900">{{ $utilisateur->enseignant->matricule }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Spécialité</p>
                <p class="font-medium text-gray-900">{{ $utilisateur->enseignant->specialite }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Date d'embauche</p>
                <p class="font-medium text-gray-900">{{ $utilisateur->enseignant->date_embauche->format('d/m/Y') }}</p>
            </div>
        </div>
        
        @if($utilisateur->enseignant->adresse)
        <div class="mt-4">
            <p class="text-sm text-gray-500 mb-1">Adresse</p>
            <p class="text-gray-900">{{ $utilisateur->enseignant->adresse }}</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Actions de gestion -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-tasks text-purple-600 mr-2"></i>
            Actions de gestion
        </h4>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('etablissement.utilisateurs.edit', $utilisateur->id) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            
            <a href="{{ route('etablissement.utilisateurs.reset-password', $utilisateur->id) }}" 
               class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-all"
               onclick="return confirm('Réinitialiser le mot de passe ? Un nouveau mot de passe sera généré et affiché.')">
                <i class="fas fa-key mr-2"></i>
                Réinitialiser mot de passe
            </a>
            
            @if($utilisateur->is_active)
                <button onclick="desactiver({{ $utilisateur->id }})" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                    <i class="fas fa-ban mr-2"></i>
                    Désactiver le compte
                </button>
            @else
                <button onclick="activer({{ $utilisateur->id }})" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all">
                    <i class="fas fa-check-circle mr-2"></i>
                    Activer le compte
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation pour désactiver -->
<div id="desactiverModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-ban text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer la désactivation</h3>
            <p class="text-gray-600 mb-6">L'utilisateur n'aura plus accès à l'application.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDesactiverModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button id="confirmDesactiver" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Désactiver
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation pour activer -->
<div id="activerModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer l'activation</h3>
            <p class="text-gray-600 mb-6">L'utilisateur pourra à nouveau se connecter.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeActiverModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button id="confirmActiver" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Activer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour les actions -->
<form id="actionForm" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
    let userId = {{ $utilisateur->id }};

    // Désactiver
    function desactiver(id) {
        document.getElementById('desactiverModal').classList.remove('hidden');
    }

    function closeDesactiverModal() {
        document.getElementById('desactiverModal').classList.add('hidden');
    }

    document.getElementById('confirmDesactiver')?.addEventListener('click', function() {
        const form = document.getElementById('actionForm');
        form.action = `/etablissement/utilisateurs/${userId}/deactivate`;
        form.appendChild(createInput('_method', 'PUT'));
        form.submit();
    });

    // Activer
    function activer(id) {
        document.getElementById('activerModal').classList.remove('hidden');
    }

    function closeActiverModal() {
        document.getElementById('activerModal').classList.add('hidden');
    }

    document.getElementById('confirmActiver')?.addEventListener('click', function() {
        const form = document.getElementById('actionForm');
        form.action = `/etablissement/utilisateurs/${userId}/activate`;
        form.appendChild(createInput('_method', 'PUT'));
        form.submit();
    });

    // Helper
    function createInput(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

    // Fermer les modals en cliquant dehors
    document.getElementById('desactiverModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDesactiverModal();
    });
    
    document.getElementById('activerModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeActiverModal();
    });

    // Fermer avec Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('desactiverModal')?.classList.contains('hidden')) {
                closeDesactiverModal();
            }
            if (!document.getElementById('activerModal')?.classList.contains('hidden')) {
                closeActiverModal();
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Styles supplémentaires si nécessaire */
</style>
@endpush
@endsection