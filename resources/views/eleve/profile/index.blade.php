{{-- resources/views/eleve/profile/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon profil - Élève')
@section('page-title', 'Mon profil')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Carte d'identité -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden sticky top-6">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-center">
                    <div class="relative inline-block">
                        @if($user->photo)
                            <img src="{{ Storage::url($user->photo) }}" alt="Photo de profil" 
                                 class="w-32 h-32 rounded-full mx-auto border-4 border-white shadow-lg object-cover">
                        @else
                            <div class="w-32 h-32 rounded-full mx-auto border-4 border-white shadow-lg bg-indigo-300 flex items-center justify-center">
                                <i class="fas fa-user-graduate text-5xl text-white"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-xl font-semibold text-white mt-3">{{ $eleve->prenom }} {{ $eleve->nom }}</h3>
                    <p class="text-indigo-100 text-sm">{{ $eleve->classe->nom ?? 'Classe non définie' }}</p>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-envelope w-5 text-indigo-500"></i>
                            <span class="ml-3">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-phone w-5 text-indigo-500"></i>
                            <span class="ml-3">{{ $user->telephone ?? 'Non renseigné' }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt w-5 text-indigo-500"></i>
                            <span class="ml-3">{{ $user->adresse ?? 'Non renseignée' }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-alt w-5 text-indigo-500"></i>
                            <span class="ml-3">Inscrit le {{ $eleve->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-id-card w-5 text-indigo-500"></i>
                            <span class="ml-3">Matricule: {{ $eleve->matricule }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('eleve.profile.edit') }}" 
                           class="w-full block text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Modifier mon profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="lg:col-span-2">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-user mr-2 text-indigo-600"></i>Informations personnelles
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Prénom</p>
                            <p class="font-medium text-gray-800">{{ $eleve->prenom }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nom</p>
                            <p class="font-medium text-gray-800">{{ $eleve->nom }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de naissance</p>
                            <p class="font-medium text-gray-800">{{ $eleve->date_naissance ? \Carbon\Carbon::parse($eleve->date_naissance)->format('d/m/Y') : 'Non renseignée' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lieu de naissance</p>
                            <p class="font-medium text-gray-800">{{ $eleve->lieu_naissance ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sexe</p>
                            <p class="font-medium text-gray-800">{{ $eleve->sexe == 'M' ? 'Masculin' : 'Féminin' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Classe</p>
                            <p class="font-medium text-gray-800">{{ $eleve->classe->nom ?? 'Non définie' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations parents -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-users mr-2 text-indigo-600"></i>Informations parents
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nom du parent</p>
                            <p class="font-medium text-gray-800">{{ $eleve->nom_parent ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email parent</p>
                            <p class="font-medium text-gray-800">{{ $eleve->email_parent ?? 'Non renseigné' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Téléphone parent</p>
                            <p class="font-medium text-gray-800">{{ $eleve->telephone_parent ?? 'Non renseigné' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-chart-line mr-2 text-indigo-600"></i>Statistiques
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_notes'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Notes</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ $stats['moyenne_generale'] ?? 0 }}/20</p>
                            <p class="text-sm text-gray-500">Moyenne</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_absences'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Absences</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ $stats['bulletins'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Bulletins</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection