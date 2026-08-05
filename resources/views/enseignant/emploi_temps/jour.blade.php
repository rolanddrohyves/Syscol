{{-- resources/views/enseignant/emploi_temps/jour.blade.php --}}
@extends('layouts.app')

@section('title', 'Emploi du temps - ' . $dateCourante->format('d/m/Y'))
@section('page-title', 'Emploi du temps du ' . $dateCourante->locale('fr')->isoFormat('dddd D MMMM YYYY'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-calendar-day mr-2"></i>
                        {{ $jourSemaine }} {{ $dateCourante->format('d/m/Y') }}
                    </h3>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('enseignant.emploi_temps.jour', $dateCourante->copy()->subDay()->format('Y-m-d')) }}" 
                       class="px-3 py-1 bg-white/20 text-white rounded-lg hover:bg-white/30">
                        <i class="fas fa-chevron-left"></i> Jour précédent
                    </a>
                    <a href="{{ route('enseignant.emploi_temps.jour', $dateCourante->copy()->addDay()->format('Y-m-d')) }}" 
                       class="px-3 py-1 bg-white/20 text-white rounded-lg hover:bg-white/30">
                        Jour suivant <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            @if($coursDuJour->count() > 0)
                <div class="space-y-3">
                    @foreach($coursDuJour as $cours)
                    <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-28 text-center">
                            <span class="text-lg font-bold text-indigo-600">
                                {{ \Carbon\Carbon::parse($cours->heure_debut)->format('H:i') }}
                            </span>
                            <span class="text-gray-400">→</span>
                            <span class="text-lg font-bold text-indigo-600">
                                {{ \Carbon\Carbon::parse($cours->heure_fin)->format('H:i') }}
                            </span>
                        </div>
                        <div class="flex-1 ml-4">
                            <p class="font-semibold text-gray-800 text-lg">{{ $cours->matiere->nom ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Classe: {{ $cours->classe->nom ?? 'N/A' }}</p>
                        </div>
                        @if($cours->salle)
                        <div class="px-3 py-2 bg-gray-200 rounded-lg text-sm text-gray-700">
                            <i class="fas fa-door-open mr-1"></i> Salle {{ $cours->salle }}
                        </div>
                        @endif
                        <div class="ml-3">
                            <a href="{{ route('enseignant.presences') }}?classe_id={{ $cours->classe_id }}" 
                               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                                <i class="fas fa-check-circle mr-1"></i> Appel
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-clock text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Aucun cours programmé ce jour</p>
                    <p class="text-gray-400 text-sm mt-2">Profitez de cette journée pour préparer vos cours</p>
                </div>
            @endif
        </div>
    </div>
    
    <div class="mt-4 text-center">
        <a href="{{ route('enseignant.emploi_temps') }}" class="text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-left mr-1"></i> Retour à l'emploi du temps hebdomadaire
        </a>
    </div>
</div>
@endsection