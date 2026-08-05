{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails utilisateur - SYSCOL')
@section('page-title', 'Détails de l\'utilisateur')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.edit', $user->id) }}" 
               class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all shadow-lg hover:shadow-xl">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            @if($user->id != auth()->id())
            <button onclick="resetPassword({{ $user->id }})" 
                    class="flex items-center px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all shadow-lg">
                <i class="fas fa-key mr-2"></i>
                Réinitialiser mot de passe
            </button>
            @endif
            <a href="{{ route('admin.users') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Grille d'informations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Avatar et statut -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg mb-4">
                        <span class="text-5xl font-bold text-white">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $user->email }}</p>
                    
                    <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $user->is_active ? 'Compte actif' : 'Compte inactif' }}
                    </span>
                    
                    @if($user->email_verified_at)
                    <span class="mt-2 text-xs text-green-600 flex items-center">
                        <i class="fas fa-check-circle mr-1"></i>
                        Email vérifié le {{ $user->email_verified_at->format('d/m/Y') }}
                    </span>
                    @else
                    <span class="mt-2 text-xs text-yellow-600 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Email non vérifié
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne droite : Informations détaillées -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-green-600 mr-2"></i>
                    Informations personnelles
                </h3>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Nom complet</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $user->name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Email</dt>
                        <dd class="text-base font-medium text-gray-900">
                            <a href="mailto:{{ $user->email }}" class="text-green-600 hover:text-green-800">
                                {{ $user->email }}
                            </a>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Téléphone</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $user->telephone ?? 'Non renseigné' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Rôle</dt>
                        <dd class="text-base">
                            <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                {{ $user->role->display_name ?? $user->role->name ?? 'N/A' }}
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Établissement</dt>
                        <dd class="text-base font-medium text-gray-900">
                            @if($user->etablissement)
                                <a href="{{ route('admin.etablissements.show', $user->etablissement_id) }}" class="text-green-600 hover:text-green-800">
                                    {{ $user->etablissement->nom }}
                                </a>
                            @else
                                Non assigné
                            @endif
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Date d'inscription</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $user->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Dernière mise à jour</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $user->updated_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Dernière connexion</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Connexions</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['connexions'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-sign-in-alt text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Actions</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['actions'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Documents</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['documents'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Notifications</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['notifications'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bell text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-history text-green-600 mr-2"></i>
            Activités récentes
        </h3>

        <div class="space-y-4">
            @forelse($user->activities ?? [] as $activity)
            <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                        <i class="fas fa-circle text-green-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">{{ $activity->description }}</p>
                        <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <span class="text-xs text-gray-400">{{ $activity->created_at->format('H:i') }}</span>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">Aucune activité récente</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal de réinitialisation de mot de passe -->
<div id="resetPasswordModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-key text-2xl text-purple-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Réinitialiser le mot de passe</h3>
            <p class="text-gray-600 mb-6">
                Voulez-vous réinitialiser le mot de passe de <strong>{{ $user->name }}</strong> ?<br>
                Un nouveau mot de passe temporaire sera généré.
            </p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeResetModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <a href="{{ route('admin.users.reset-password', $user->id) }}" 
                   class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Confirmer
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function resetPassword(id) {
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }

    function closeResetModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
    }

    // Fermer le modal en cliquant dehors
    document.getElementById('resetPasswordModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeResetModal();
        }
    });
</script>
@endpush