{{-- resources/views/etablissement/utilisateurs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des utilisateurs - SYSCOL')
@section('page-title', 'Gestion des utilisateurs')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-users-cog text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Utilisateurs</h2>
                <p class="text-sm text-gray-500">Gestion du personnel de l'établissement</p>
            </div>
        </div>
        
        <a href="{{ route('etablissement.utilisateurs.create') }}" 
           class="flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg">
            <i class="fas fa-user-plus mr-2"></i>
            Nouvel utilisateur
        </a>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total personnel</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Actifs</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['actifs'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Inactifs</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['inactifs'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-pause-circle text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Administrateurs</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['admins'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-crown text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('etablissement.utilisateurs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Rechercher un utilisateur..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->display_name ?? $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactif" {{ request('status') == 'inactif' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('etablissement.utilisateurs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dernière connexion</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($utilisateurs as $utilisateur)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold mr-3">
                                    {{ substr($utilisateur->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $utilisateur->name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $utilisateur->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $utilisateur->email }}</p>
                            @if($utilisateur->telephone)
                                <p class="text-xs text-gray-400">{{ $utilisateur->telephone }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($utilisateur->role->name == 'admin_etablissement') bg-red-100 text-red-800
                                @elseif($utilisateur->role->name == 'directeur_etudes') bg-blue-100 text-blue-800
                                @elseif($utilisateur->role->name == 'cpe') bg-yellow-100 text-yellow-800
                                @elseif($utilisateur->role->name == 'comptable') bg-green-100 text-green-800
                                @elseif($utilisateur->role->name == 'enseignant') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $utilisateur->role->display_name ?? $utilisateur->role->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($utilisateur->is_active)
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Actif
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-circle text-xs mr-1"></i> Inactif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($utilisateur->last_login_at)
                                {{ $utilisateur->last_login_at->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-400">Jamais</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('etablissement.utilisateurs.show', $utilisateur->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('etablissement.utilisateurs.edit', $utilisateur->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('etablissement.utilisateurs.reset-password', $utilisateur->id) }}" 
                                   class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100"
                                   title="Réinitialiser mot de passe"
                                   onclick="return confirm('Réinitialiser le mot de passe ? Un nouveau mot de passe sera généré.')">
                                    <i class="fas fa-key"></i>
                                </a>
                                @if($utilisateur->is_active)
                                    <button onclick="desactiver({{ $utilisateur->id }})" 
                                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                            title="Désactiver">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button onclick="activer({{ $utilisateur->id }})" 
                                            class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100"
                                            title="Activer">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-users-slash text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun utilisateur</h3>
                                <p class="text-gray-500 mb-4">Commencez par ajouter un utilisateur</p>
                                <a href="{{ route('etablissement.utilisateurs.create') }}" 
                                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter un utilisateur
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($utilisateurs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $utilisateurs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Répartition par rôle -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
            Répartition par rôle
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach($roles as $role)
                @php
                    $count = $utilisateurs->filter(function($u) use ($role) {
                        return $u->role->name === $role->name;
                    })->count();
                    $total = $stats['total'] ?? 1;
                    $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                    
                    $colors = [
                        'admin_etablissement' => 'red',
                        'directeur_etudes' => 'blue',
                        'cpe' => 'yellow',
                        'comptable' => 'green',
                        'enseignant' => 'purple',
                    ];
                    $color = $colors[$role->name] ?? 'gray';
                @endphp
                <div class="text-center p-3 bg-{{ $color }}-50 rounded-xl">
                    <span class="text-2xl font-bold text-{{ $color }}-600">{{ $count }}</span>
                    <p class="text-xs text-gray-600 mt-1">{{ $role->display_name ?? $role->name }}</p>
                    <p class="text-xs text-{{ $color }}-500">{{ $percentage }}%</p>
                </div>
            @endforeach
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
            <p class="text-gray-600 mb-6">Cet utilisateur n'aura plus accès à l'application.</p>
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
            <p class="text-gray-600 mb-6">Cet utilisateur pourra à nouveau se connecter.</p>
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
    let userId = null;

    // Désactiver
    function desactiver(id) {
        userId = id;
        document.getElementById('desactiverModal').classList.remove('hidden');
    }

    function closeDesactiverModal() {
        document.getElementById('desactiverModal').classList.add('hidden');
        userId = null;
    }

    document.getElementById('confirmDesactiver')?.addEventListener('click', function() {
        if (userId) {
            const form = document.getElementById('actionForm');
            form.action = `/etablissement/utilisateurs/${userId}/desactiver`;
            form.appendChild(createInput('_method', 'PUT'));
            form.submit();
        }
    });

    // Activer
    function activer(id) {
        userId = id;
        document.getElementById('activerModal').classList.remove('hidden');
    }

    function closeActiverModal() {
        document.getElementById('activerModal').classList.add('hidden');
        userId = null;
    }

    document.getElementById('confirmActiver')?.addEventListener('click', function() {
        if (userId) {
            const form = document.getElementById('actionForm');
            form.action = `/etablissement/utilisateurs/${userId}/activer`;
            form.appendChild(createInput('_method', 'PUT'));
            form.submit();
        }
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
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>
@endpush
@endsection