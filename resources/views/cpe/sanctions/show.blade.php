{{-- resources/views/cpe/sanctions/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails de la sanction - CPE')
@section('page-title', 'Détails de la sanction')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-gray-100">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-gavel text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Détails de la sanction</h2>
                    <p class="text-gray-500">Référence #SAN-{{ str_pad($sanction->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('cpe.sanctions.edit', $sanction->id) }}" 
                   class="flex items-center px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-all">
                    <i class="fas fa-edit mr-2"></i>
                    Modifier
                </a>
                <a href="{{ route('cpe.sanctions.index') }}" 
                   class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Retour
                </a>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne gauche : Élève -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-user-graduate mr-2"></i>
                        Élève concerné
                    </h3>
                    
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-3xl font-bold">
                            {{ substr($sanction->eleve->prenom, 0, 1) }}{{ substr($sanction->eleve->nom, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xl font-bold">{{ $sanction->eleve->prenom }} {{ $sanction->eleve->nom }}</p>
                            <p class="text-white/80">{{ $sanction->eleve->classe->nom }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-white/20 pt-4 mt-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-white/80">Matricule</span>
                            <span class="font-medium">{{ $sanction->eleve->matricule }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-white/80">Date naissance</span>
                            <span class="font-medium">{{ $sanction->eleve->date_naissance->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mt-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Actions rapides</h4>
                    <div class="space-y-2">
                        <a href="{{ route('cpe.absences.create', ['eleve_id' => $sanction->eleve->id]) }}" 
                           class="flex items-center p-3 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                            <i class="fas fa-calendar-times text-red-600 mr-3"></i>
                            <span class="text-sm text-gray-700">Signaler une absence</span>
                        </a>
                        <a href="{{ route('cpe.retards.create', ['eleve_id' => $sanction->eleve->id]) }}" 
                           class="flex items-center p-3 bg-yellow-50 rounded-xl hover:bg-yellow-100 transition-colors">
                            <i class="fas fa-clock text-yellow-600 mr-3"></i>
                            <span class="text-sm text-gray-700">Signaler un retard</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Détails de la sanction -->
            <div class="lg:col-span-2">
                <!-- Statut et type -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <div class="flex flex-wrap gap-4 mb-4">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Statut</p>
                            <span class="px-4 py-2 text-sm rounded-full inline-block
                                @if($sanction->statut == 'en_cours') bg-yellow-100 text-yellow-800
                                @elseif($sanction->statut == 'executee') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                <i class="fas fa-circle text-xs mr-2"></i>
                                {{ $statuts[$sanction->statut] ?? $sanction->statut }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Type de sanction</p>
                            <span class="px-4 py-2 text-sm rounded-full inline-block
                                @if($sanction->type == 'avertissement') bg-yellow-100 text-yellow-800
                                @elseif($sanction->type == 'retenue') bg-orange-100 text-orange-800
                                @elseif($sanction->type == 'exclusion_temporaire') bg-red-100 text-red-800
                                @elseif($sanction->type == 'exclusion_definitive') bg-red-200 text-red-900
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ $types[$sanction->type] ?? $sanction->type }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Date</p>
                            <p class="font-medium text-gray-900">{{ $sanction->date->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    @if($sanction->duree)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-500 mb-1">Durée</p>
                        <p class="font-medium text-gray-900">{{ $sanction->duree }} heures</p>
                    </div>
                    @endif
                </div>

                <!-- Motif -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-purple-600 mr-2"></i>
                        Motif de la sanction
                    </h4>
                    <p class="text-gray-700">{{ $sanction->motif }}</p>
                </div>

                <!-- Description complémentaire -->
                @if($sanction->description)
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-align-left text-purple-600 mr-2"></i>
                        Description complémentaire
                    </h4>
                    <p class="text-gray-700">{{ $sanction->description }}</p>
                </div>
                @endif

                <!-- Période d'application -->
                @if($sanction->date_debut || $sanction->date_fin)
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                        Période d'application
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        @if($sanction->date_debut)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date de début</p>
                            <p class="font-medium text-gray-900">{{ $sanction->date_debut->format('d/m/Y') }}</p>
                        </div>
                        @endif
                        
                        @if($sanction->date_fin)
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Date de fin</p>
                            <p class="font-medium text-gray-900">{{ $sanction->date_fin->format('d/m/Y') }}</p>
                        </div>
                        @endif
                    </div>
                    
                    @if($sanction->date_debut && $sanction->date_fin)
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                            <span>Début</span>
                            <span>Fin</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            @php
                                $aujourdhui = now();
                                $debut = \Carbon\Carbon::parse($sanction->date_debut);
                                $fin = \Carbon\Carbon::parse($sanction->date_fin);
                                $totalJours = $debut->diffInDays($fin);
                                $joursPasses = $debut->diffInDays($aujourdhui);
                                $pourcentage = $totalJours > 0 ? min(100, max(0, round(($joursPasses / $totalJours) * 100))) : 0;
                            @endphp
                            <div class="h-2.5 rounded-full bg-purple-500" style="width: {{ $pourcentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">
                            Progression : {{ $pourcentage }}%
                        </p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Historique des modifications -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mt-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-history text-purple-600 mr-2"></i>
                        Informations système
                    </h4>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créé le</span>
                            <span class="font-medium text-gray-900">{{ $sanction->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dernière modification</span>
                            <span class="font-medium text-gray-900">{{ $sanction->updated_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover\:-translate-y-1:hover {
        transform: translateY(-4px);
    }
</style>
@endpush