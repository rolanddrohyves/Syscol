{{-- resources/views/eleve/emploi-temps.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon emploi du temps - Élève')
@section('page-title', 'Mon emploi du temps')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    @if(isset($error))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        {{ $error }}
    </div>
    @endif

    @if(isset($eleve))
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Mon emploi du temps
                    </h3>
                    <p class="text-indigo-100 text-sm mt-1">
                        {{ $eleve->prenom }} {{ $eleve->nom }} • Classe: {{ $eleve->classe->nom ?? 'Non définie' }}
                    </p>
                </div>
                <div class="text-right text-white">
                    <p class="text-sm">Semaine du</p>
                    <p class="font-semibold">{{ now()->startOfWeek()->format('d/m/Y') }} au {{ now()->endOfWeek()->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Emploi du temps par jour (vue carte) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($jours as $jour)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 bg-gradient-to-r from-indigo-400 to-purple-500">
                <h4 class="font-semibold text-white">{{ $jour }}</h4>
            </div>
            <div class="divide-y divide-gray-200">
                @php
                    $coursDuJour = isset($emploiDuTemps[$jour]) ? $emploiDuTemps[$jour] : collect();
                @endphp
                
                @if($coursDuJour->count() > 0)
                    @foreach($coursDuJour as $cours)
                    <div class="p-3 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $cours->matiere->nom ?? 'Cours' }}</p>
                                <p class="text-xs text-gray-500">{{ $cours->enseignant->name ?? 'Professeur' }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <p class="text-xs text-gray-400">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($cours->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($cours->heure_fin)->format('H:i') }}
                                    </p>
                                    @if($cours->salle)
                                    <p class="text-xs text-gray-400">
                                        <i class="fas fa-door-open mr-1"></i>
                                        Salle: {{ $cours->salle }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-gray-400">
                        <i class="fas fa-clock text-2xl mb-2 block"></i>
                        <p class="text-sm">Aucun cours</p>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Légende -->
    <div class="mt-6 bg-gray-50 rounded-xl p-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Informations</h4>
        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-indigo-500 rounded-full mr-2"></div>
                <span>Cours programmé</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                <span>Emploi du temps de votre classe</span>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-12 text-center">
            <i class="fas fa-calendar-alt text-5xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-500">Profil élève non trouvé.</p>
            <p class="text-gray-400 text-sm mt-2">Veuillez contacter l'administrateur.</p>
        </div>
    </div>
    @endif
</div>
@endsection