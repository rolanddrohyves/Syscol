{{-- resources/views/parent/enfants/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes enfants - Parent')
@section('page-title', 'Mes enfants')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Mes enfants scolarisés</h2>
                <p class="text-gray-500">Liste de tous vos enfants inscrits dans l'établissement</p>
            </div>
            <div class="bg-indigo-100 rounded-full px-4 py-2">
                <span class="text-indigo-700 font-semibold">{{ $enfants->count() }} enfant(s)</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($enfants as $enfant)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <!-- Photo et informations -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                        @if($enfant->photo)
                            <img src="{{ Storage::url($enfant->photo) }}" alt="{{ $enfant->prenom }}" class="w-16 h-16 rounded-full object-cover">
                        @else
                            <i class="fas fa-user-graduate text-white text-3xl"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $enfant->prenom }} {{ $enfant->nom }}</h3>
                        <p class="text-indigo-100 text-sm">Matricule: {{ $enfant->matricule ?? 'N/A' }}</p>
                        <p class="text-indigo-100 text-sm">Classe: {{ $enfant->classe->nom ?? 'Non définie' }}</p>
                    </div>
                </div>
            </div>

            <!-- Informations -->
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $enfant->notes->avg('note') ?? 0 }}/20</p>
                        <p class="text-xs text-gray-500">Moyenne générale</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $enfant->absences->count() }}</p>
                        <p class="text-xs text-gray-500">Absences totales</p>
                    </div>
                </div>

                <div class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date de naissance</span>
                        <span class="font-medium">{{ $enfant->date_naissance ? \Carbon\Carbon::parse($enfant->date_naissance)->format('d/m/Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lieu de naissance</span>
                        <span class="font-medium">{{ $enfant->lieu_naissance ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Statut</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $enfant->status == 'actif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ ucfirst($enfant->status ?? 'Actif') }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-2 mt-4 pt-3 border-t">
                    <a href="{{ route('parent.enfants.show', $enfant->id) }}" 
                       class="flex-1 text-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-eye mr-1"></i> Détails
                    </a>
                    <a href="{{ route('parent.notes.enfant', $enfant->id) }}" 
                       class="flex-1 text-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                        <i class="fas fa-star mr-1"></i> Notes
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-child text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucun enfant trouvé</h3>
                <p class="text-gray-500">Aucun enfant n'est associé à votre compte.</p>
                <p class="text-gray-400 text-sm mt-2">Contactez l'administration pour lier vos enfants à votre compte.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection