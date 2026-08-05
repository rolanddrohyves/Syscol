{{-- resources/views/etablissement/annes-scolaires/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails année scolaire - SYSCOL')
@section('page-title', 'Détails de l\'année scolaire')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $anneeScolaire->libelle }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $anneeScolaire->date_debut->format('d/m/Y') }} - {{ $anneeScolaire->date_fin->format('d/m/Y') }}
                </p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            @if(!$anneeScolaire->is_current)
            <form action="{{ route('etablissement.annes_scolaires.set-current', $anneeScolaire->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all shadow-lg"
                        onclick="return confirm('Définir cette année comme année en cours ?')">
                    <i class="fas fa-play-circle mr-2"></i>
                    Définir comme année en cours
                </button>
            </form>
            @endif
            
            <a href="{{ route('etablissement.annes_scolaires.edit', $anneeScolaire->id) }}" 
               class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Modifier
            </a>
            
            <a href="{{ route('etablissement.annes_scolaires.index') }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Statut</p>
                    @if($anneeScolaire->is_current)
                        <p class="text-2xl font-bold text-green-600">En cours</p>
                    @elseif($anneeScolaire->date_fin < now())
                        <p class="text-2xl font-bold text-gray-600">Terminée</p>
                    @else
                        <p class="text-2xl font-bold text-blue-600">À venir</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-info-circle text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Trimestres</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_trimestres'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Durée</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['duree_jours'] }} jours</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Progression</p>
                    @php
                        $aujourdhui = now();
                        $debut = Carbon\Carbon::parse($anneeScolaire->date_debut);
                        $fin = Carbon\Carbon::parse($anneeScolaire->date_fin);
                        $totalJours = $debut->diffInDays($fin);
                        $joursPasses = $debut->diffInDays($aujourdhui);
                        $pourcentage = $totalJours > 0 ? min(100, max(0, round(($joursPasses / $totalJours) * 100))) : 0;
                    @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ $pourcentage }}%</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de progression -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700">Progression de l'année</span>
            <span class="text-sm font-medium text-gray-700">{{ $pourcentage }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="h-4 rounded-full {{ $pourcentage >= 75 ? 'bg-green-500' : ($pourcentage >= 50 ? 'bg-blue-500' : ($pourcentage >= 25 ? 'bg-yellow-500' : 'bg-red-500')) }}" 
                 style="width: {{ $pourcentage }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span>{{ $anneeScolaire->date_debut->format('d/m/Y') }}</span>
            <span>Aujourd'hui</span>
            <span>{{ $anneeScolaire->date_fin->format('d/m/Y') }}</span>
        </div>
    </div>

    <!-- Liste des trimestres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-layer-group text-indigo-600 mr-2"></i>
            Trimestres de l'année
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($anneeScolaire->trimestres->sortBy('numero') as $trimestre)
                <div class="border border-gray-200 rounded-xl overflow-hidden {{ $trimestre->is_current ? 'ring-2 ring-green-500' : '' }}">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-4 py-3">
                        <div class="flex justify-between items-center">
                            <h4 class="text-white font-semibold">{{ $trimestre->libelle }}</h4>
                            @if($trimestre->is_current)
                                <span class="px-2 py-1 text-xs bg-green-500 text-white rounded-full">En cours</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Début :</span>
                            <span class="font-medium text-gray-800">{{ $trimestre->date_debut->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Fin :</span>
                            <span class="font-medium text-gray-800">{{ $trimestre->date_fin->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Durée :</span>
                            <span class="font-medium text-gray-800">{{ $trimestre->date_debut->diffInDays($trimestre->date_fin) }} jours</span>
                        </div>
                        
                        @php
                            $aujourdhui = now();
                            $debutTrimestre = Carbon\Carbon::parse($trimestre->date_debut);
                            $finTrimestre = Carbon\Carbon::parse($trimestre->date_fin);
                            $totalJoursTrimestre = $debutTrimestre->diffInDays($finTrimestre);
                            $joursPassesTrimestre = $debutTrimestre->diffInDays($aujourdhui);
                            $progressionTrimestre = $totalJoursTrimestre > 0 ? min(100, max(0, round(($joursPassesTrimestre / $totalJoursTrimestre) * 100))) : 0;
                        @endphp
                        
                        @if($progressionTrimestre > 0 && $progressionTrimestre < 100 && !$trimestre->is_current)
                            <div class="mt-2">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progression</span>
                                    <span>{{ $progressionTrimestre }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-blue-500" style="width: {{ $progressionTrimestre }}%"></div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex justify-end space-x-2 pt-2 border-t border-gray-100">
                            <a href="#" class="text-xs text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-chart-bar mr-1"></i> Statistiques
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-8 text-gray-500">
                    <i class="fas fa-layer-group text-3xl mb-2 block text-gray-400"></i>
                    <p>Aucun trimestre pour cette année scolaire</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Informations complémentaires -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Résumé des événements -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-calendar-check text-indigo-600 mr-2"></i>
                Jours clés
            </h3>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-700">Rentrée scolaire</span>
                    <span class="font-medium text-gray-900">{{ $anneeScolaire->date_debut->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm text-gray-700">Fin des cours</span>
                    <span class="font-medium text-gray-900">{{ $anneeScolaire->date_fin->format('d/m/Y') }}</span>
                </div>
                @if($stats['trimestre_en_cours'])
                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg border border-green-200">
                    <span class="text-sm text-green-700">Trimestre en cours</span>
                    <span class="font-medium text-green-900">{{ $stats['trimestre_en_cours']->libelle }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bolt text-indigo-600 mr-2"></i>
                Actions rapides
            </h3>
            
            <div class="grid grid-cols-2 gap-3">
                <a href="#" class="p-3 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-colors text-center">
                    <i class="fas fa-file-pdf text-xl mb-1 block"></i>
                    <span class="text-xs">Exporter rapport</span>
                </a>
                <a href="#" class="p-3 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 transition-colors text-center">
                    <i class="fas fa-chart-line text-xl mb-1 block"></i>
                    <span class="text-xs">Statistiques</span>
                </a>
                <a href="#" class="p-3 bg-purple-50 text-purple-600 rounded-xl hover:bg-purple-100 transition-colors text-center">
                    <i class="fas fa-calendar-plus text-xl mb-1 block"></i>
                    <span class="text-xs">Ajouter trimestre</span>
                </a>
                <a href="#" class="p-3 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition-colors text-center">
                    <i class="fas fa-print text-xl mb-1 block"></i>
                    <span class="text-xs">Imprimer</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Aucun script spécifique nécessaire pour cette vue
</script>
@endpush

@push('styles')
<style>
    /* Styles supplémentaires si nécessaire */
</style>
@endpush