{{-- resources/views/admin/etablissements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails de l\'établissement - SYSCOL')
@section('page-title', 'Détails de l\'établissement')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-school text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $etablissement->nom }}</h2>
                <p class="text-sm text-gray-500">{{ $etablissement->code_etablissement }} · {{ $etablissement->type }}</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.etablissements.edit', $etablissement->id) }}" 
               class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all shadow-lg hover:shadow-xl">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            <a href="{{ route('admin.etablissements') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Grille d'informations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Logo et statut -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg mb-4 overflow-hidden">
                        @if($etablissement->logo)
                            <img src="{{ Storage::url($etablissement->logo) }}" alt="{{ $etablissement->nom }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-school text-5xl text-white"></i>
                        @endif
                    </div>
                    <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $etablissement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $etablissement->is_active ? 'Établissement actif' : 'Établissement inactif' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Informations détaillées -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                    Informations générales
                </h3>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Nom</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $etablissement->nom }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Type</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $etablissement->type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Code établissement</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $etablissement->code_etablissement }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Téléphone</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $etablissement->telephone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Email</dt>
                        <dd class="text-base font-medium text-gray-900">
                            <a href="mailto:{{ $etablissement->email }}" class="text-indigo-600 hover:text-indigo-800">
                                {{ $etablissement->email }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Académie</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $etablissement->academie }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Inspectorat</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $etablissement->inspectorat ?? 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Date de création</dt>
                        <dd class="text-base font-medium text-gray-900">{{ $etablissement->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>

                <div class="mt-4">
                    <dt class="text-sm text-gray-500">Adresse</dt>
                    <dd class="text-base font-medium text-gray-900">{{ $etablissement->adresse }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Classes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_classes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Élèves</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_eleves'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Enseignants</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_enseignants'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Administrateurs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_admins'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des classes -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-door-open text-indigo-600 mr-2"></i>
            Classes
        </h3>

        @if($etablissement->classes->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Série</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effectif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Professeur principal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($etablissement->classes as $classe)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $classe->nom }}</td>
                        <td class="px-6 py-4">{{ $classe->niveau }}</td>
                        <td class="px-6 py-4">{{ $classe->serie ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $classe->capacite }}</td>
                        <td class="px-6 py-4">{{ $classe->eleves_count }}</td>
                        <td class="px-6 py-4">{{ $classe->professeurPrincipal->name ?? 'Non assigné' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Aucune classe dans cet établissement</p>
        @endif
    </div>

    <!-- Liste des utilisateurs -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-users text-indigo-600 mr-2"></i>
            Utilisateurs
        </h3>

        @if($etablissement->users->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($etablissement->users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">{{ $user->role->display_name ?? $user->role->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Aucun utilisateur dans cet établissement</p>
        @endif
    </div>
</div>
@endsection