{{-- resources/views/admin/roles/users.blade.php --}}
@extends('layouts.app')

@section('title', 'Utilisateurs du rôle - SYSCOL')
@section('page-title', 'Utilisateurs du rôle : ' . $role->display_name)

@section('content')
<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $role->display_name }}</h2>
                <p class="text-sm text-gray-500">{{ $users->total() }} utilisateur(s) avec ce rôle</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.roles.show', $role->id) }}" 
               class="flex items-center px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au rôle
            </a>
            <a href="{{ route('admin.roles.index') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-list mr-2"></i>
                Tous les rôles
            </a>
        </div>
    </div>

    <!-- Cartes de statistiques améliorées -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total utilisateurs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $users->total() }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-users text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Utilisateurs actifs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $users->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                Taux d'activité : {{ $users->total() > 0 ? round(($users->where('is_active', true)->count() / $users->total()) * 100, 1) : 0 }}%
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Niveau du rôle</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $role->level }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-arrow-up text-2xl text-blue-600"></i>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                Hiérarchie {{ $role->level >= 5 ? 'Haute' : ($role->level >= 3 ? 'Moyenne' : 'Basse') }}
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Établissements</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $users->pluck('etablissement_id')->unique()->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-school text-2xl text-amber-600"></i>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-500">
                Répartis dans {{ $users->pluck('etablissement_id')->unique()->count() }} établissement(s)
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('admin.roles.users', $role->id) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nom, email..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('admin.roles.users', $role->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Établissement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscription</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center text-white font-bold mr-3 shadow-sm">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            @if($user->telephone)
                                <div class="text-xs text-gray-500">{{ $user->telephone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->etablissement)
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $user->etablissement->nom }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Non assigné</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-circle text-xs mr-1 {{ $user->is_active ? 'text-green-500' : 'text-gray-400' }}"></i>
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div>{{ $user->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.users.reset-password', $user->id) }}" 
                                   class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition-colors"
                                   title="Réinitialiser mot de passe"
                                   onclick="return confirm('Réinitialiser le mot de passe ? Un nouveau mot de passe sera généré.')">
                                    <i class="fas fa-key"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-users text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun utilisateur</h3>
                                <p class="text-gray-500 mb-4">Aucun utilisateur n'a ce rôle pour le moment.</p>
                                <a href="{{ route('admin.users.create', ['role_id' => $role->id]) }}" 
                                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
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
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Graphique de répartition (optionnel) -->
    @if($users->count() > 10)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Répartition par établissement</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $etablissements = $users->groupBy(function($user) {
                    return $user->etablissement->nom ?? 'Non assigné';
                });
            @endphp
            
            @foreach($etablissements as $nom => $groupe)
                @php
                    $pourcentage = round(($groupe->count() / $users->total()) * 100, 1);
                    $couleurs = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500'];
                    $couleur = $couleurs[$loop->index % count($couleurs)];
                @endphp
                <div class="text-center p-3 bg-gray-50 rounded-xl">
                    <p class="text-sm font-medium text-gray-800 truncate" title="{{ $nom }}">{{ $nom }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $groupe->count() }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="h-2 rounded-full {{ $couleur }}" style="width: {{ $pourcentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $pourcentage }}%</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Animation pour les statistiques
    document.addEventListener('DOMContentLoaded', function() {
        const stats = document.querySelectorAll('.border-l-4');
        stats.forEach((stat, index) => {
            setTimeout(() => {
                stat.classList.add('scale-in');
            }, index * 100);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .scale-in {
        animation: scaleIn 0.3s ease-in-out;
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0.95);
            opacity: 0.5;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
@endpush