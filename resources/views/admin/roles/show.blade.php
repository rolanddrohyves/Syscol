{{-- resources/views/admin/roles/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails du rôle - SYSCOL')
@section('page-title', 'Détails du rôle')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-tag text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $role->display_name }}</h2>
                <p class="text-sm text-gray-500">{{ $role->name }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            @if(!in_array($role->name, ['super_admin', 'admin_etablissement', 'enseignant', 'eleve', 'parent', 'comptable', 'cpe', 'directeur_etudes']))
                <a href="{{ route('admin.roles.edit', $role->id) }}" 
                   class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
            @endif
            <a href="{{ route('admin.roles') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Grille d'informations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Informations générales -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                    Informations
                </h3>
                
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">Nom technique</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $role->name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Nom affiché</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $role->display_name }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Description</dt>
                        <dd class="text-base text-gray-700">{{ $role->description ?? 'Aucune description' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Niveau hiérarchique</dt>
                        <dd class="text-base">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                Niveau {{ $role->level }}
                            </span>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Date de création</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $role->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    
                    <div>
                        <dt class="text-sm text-gray-500">Dernière mise à jour</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $role->updated_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Colonne droite : Statistiques -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                    Statistiques
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm text-gray-600">Total utilisateurs</p>
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                        </div>
                        <p class="text-4xl font-bold text-gray-800">{{ $stats['total_utilisateurs'] }}</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm text-gray-600">Utilisateurs actifs</p>
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>
                        <p class="text-4xl font-bold text-gray-800">{{ $stats['utilisateurs_actifs'] }}</p>
                    </div>
                    
                    @if($stats['dernier_utilisateur'])
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 md:col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm text-gray-600">Dernier utilisateur</p>
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-800">{{ $stats['dernier_utilisateur']->name }}</p>
                        <p class="text-sm text-gray-500">{{ $stats['dernier_utilisateur']->created_at->diffForHumans() }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des utilisateurs du rôle -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-users text-purple-600 mr-2"></i>
                Utilisateurs avec ce rôle
            </h3>
            <a href="{{ route('admin.roles.users', $role->id) }}" class="text-sm text-purple-600 hover:text-purple-800">
                Voir tous
                <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        @if($role->users->count() > 0)
        <div class="space-y-3">
            @foreach($role->users as $user)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center text-white font-bold mr-3">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.users.show', $user->id) }}" class="text-purple-600 hover:text-purple-800">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-8">Aucun utilisateur n'a ce rôle pour le moment.</p>
        @endif
    </div>
</div>
@endsection