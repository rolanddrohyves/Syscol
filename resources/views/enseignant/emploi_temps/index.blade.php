{{-- resources/views/enseignant/emploi_temps/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon emploi du temps - Enseignant')
@section('page-title', 'Mon emploi du temps')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtres et navigation -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex space-x-2">
                <a href="{{ route('enseignant.emploi_temps.semaine', ['semaine' => $semaine - 1, 'annee' => $annee, 'classe_id' => $classeId]) }}" 
                   class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-chevron-left"></i> Semaine précédente
                </a>
                <a href="{{ route('enseignant.emploi_temps.index') }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Semaine actuelle
                </a>
                <a href="{{ route('enseignant.emploi_temps.semaine', ['semaine' => $semaine + 1, 'annee' => $annee, 'classe_id' => $classeId]) }}" 
                   class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Semaine suivante <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <div class="flex items-center space-x-4">
                <form method="GET" action="{{ route('enseignant.emploi_temps.index') }}" class="flex items-center space-x-2">
                    <select name="classe_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ $classeId == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="semaine" value="{{ $semaine }}">
                    <input type="hidden" name="annee" value="{{ $annee }}">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </form>
                
                <a href="{{ route('enseignant.emploi_temps.create') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-plus-circle"></i> Ajouter un cours
                </a>
                
                <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>
        
        <div class="text-center mt-3 text-gray-600">
            <strong>Semaine {{ $semaine }}</strong> - 
            {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
        </div>
    </div>

    <!-- Emploi du temps tableau -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-indigo-800 text-white">
                        <th class="px-4 py-3 text-center font-bold text-sm w-28 border-r border-indigo-600">HORAIRES</th>
                        @foreach($jours as $jour)
                        <th class="px-4 py-3 text-center font-bold text-sm border-r border-indigo-600">
                            {{ $jour }}
                            <span class="block text-xs font-normal text-indigo-200">
                                @php
                                    $date = null;
                                    $joursMap = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                    foreach($joursMap as $index => $j) {
                                        if ($j == $jour) {
                                            $date = $dateDebut->copy()->addDays($index);
                                            break;
                                        }
                                    }
                                @endphp
                                @if($date)
                                    {{ $date->format('d/m/Y') }}
                                @endif
                            </span>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($plagesHoraires as $heureKey => $plage)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-4 font-bold bg-gray-100 text-sm border-r border-gray-200 text-center">
                            {{ $plage }}
                        </td>
                        @foreach($jours as $jour)
                        @php
                            // Chercher le cours pour ce jour et cette heure (en ignorant les secondes)
                            $cours = null;
                            foreach($emploiParJour[$jour] as $c) {
                                $heureCours = substr($c->heure_debut, 0, 5); // Prend seulement HH:MM
                                if ($heureCours == $heureKey) {
                                    $cours = $c;
                                    break;
                                }
                            }
                        @endphp
                        <td class="px-2 py-2 text-center border-r border-gray-200 align-top {{ $cours ? 'bg-blue-50' : 'bg-gray-50' }}" style="min-height: 80px;">
                            @if($cours)
                                <div class="flex flex-col items-center justify-center space-y-1">
                                    <div class="font-bold text-indigo-700 text-sm">{{ $cours->matiere->nom ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-600">
                                        <i class="fas fa-users"></i> {{ $cours->classe->nom ?? '' }}
                                    </div>
                                    @if($cours->salle)
                                        <div class="text-xs text-gray-500">
                                            <i class="fas fa-door-open"></i> {{ $cours->salle }}
                                        </div>
                                    @endif
                                    <div class="mt-1">
                                        <form action="{{ route('enseignant.emploi_temps.destroy', $cours->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Supprimer ce cours ?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-300 text-sm py-2">
                                    <i class="fas fa-minus-circle"></i>
                                </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Légende -->
    <div class="mt-4 flex flex-wrap justify-center gap-6 text-xs">
        <div class="flex items-center">
            <span class="inline-block w-4 h-4 bg-blue-100 border border-blue-300 rounded mr-2"></span>
            <span class="text-gray-600">Cours programmé</span>
        </div>
        <div class="flex items-center">
            <span class="inline-block w-4 h-4 bg-gray-100 border border-gray-300 rounded mr-2"></span>
            <span class="text-gray-600">Pas de cours</span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .no-print, 
        .sidebar, 
        header, 
        footer,
        button,
        form,
        .flex.space-x-2:has(button),
        .flex.items-center.space-x-4 {
            display: none !important;
        }
        
        body {
            margin: 0;
            padding: 0;
            background: white;
        }
        
        .max-w-7xl {
            max-width: 100%;
            margin: 0;
            padding: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            border: 1px solid #000 !important;
            padding: 8px;
        }
        
        th {
            background-color: #1e3a8a !important;
            color: white !important;
        }
        
        .bg-blue-50 {
            background-color: #eff6ff !important;
        }
        
        .text-indigo-700 {
            color: #1e3a8a !important;
        }
        
        button, .bg-green-600, .bg-blue-600, .bg-gray-600 {
            display: none !important;
        }
        
        .shadow-lg {
            box-shadow: none !important;
        }
    }
    
    table {
        border-collapse: collapse;
    }
    
    td, th {
        vertical-align: top;
    }
    
    .bg-blue-50 {
        background-color: #eff6ff;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb;
    }
</style>
@endpush