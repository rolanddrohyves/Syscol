{{-- resources/views/comptable/frais/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails du frais - SYSCOL')
@section('page-title', 'Détails du frais de scolarité')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-tag text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $frais->libelle }}</h1>
                        <p class="text-blue-100">{{ $frais->anneeScolaire->libelle ?? 'N/A' }}</p>
                    </div>
                </div>
                <div>
                    @if($frais->obligatoire)
                        <span class="px-4 py-2 bg-green-500 text-white rounded-full text-sm">Obligatoire</span>
                    @else
                        <span class="px-4 py-2 bg-gray-500 text-white rounded-full text-sm">Optionnel</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-8 py-4 bg-gray-50 border-b border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('comptable.frais.edit', $frais->id) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
            <a href="{{ route('comptable.frais.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <!-- Corps -->
        <div class="p-8">
            <!-- Informations principales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-50 p-4 rounded-xl">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Type</h3>
                    <p class="text-lg font-semibold text-gray-800">
                        @switch($frais->type)
                            @case('inscription') Inscription @break
                            @case('scolarite') Scolarité @break
                            @case('cantine') Cantine @break
                            @case('transport') Transport @break
                            @default Autre
                        @endswitch
                    </p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-xl">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Périodicité</h3>
                    <p class="text-lg font-semibold text-gray-800">
                        @switch($frais->periodicite)
                            @case('mensuel') Mensuel @break
                            @case('trimestriel') Trimestriel @break
                            @case('annuel') Annuel @break
                            @case('unique') Unique @break
                        @endswitch
                    </p>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-xl">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Montant</h3>
                    <p class="text-lg font-bold text-blue-600">{{ number_format($frais->montant, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            <!-- Description -->
            @if($frais->description)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Description</h3>
                <p class="text-gray-700 bg-gray-50 p-4 rounded-xl">{{ $frais->description }}</p>
            </div>
            @endif

            <!-- Statistiques -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistiques</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 p-4 rounded-xl text-center">
                        <p class="text-2xl font-bold text-green-600">{{ number_format($stats['total_paye'], 0, ',', ' ') }} FCFA</p>
                        <p class="text-sm text-gray-600">Total perçu</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-xl text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['nombre_paiements'] }}</p>
                        <p class="text-sm text-gray-600">Nombre de paiements</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-xl text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['moyenne'], 0, ',', ' ') }} FCFA</p>
                        <p class="text-sm text-gray-600">Moyenne par paiement</p>
                    </div>
                </div>
            </div>

            <!-- Derniers paiements -->
            @if($frais->paiements()->count() > 0)
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Derniers paiements</h3>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Date</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Élève</th>
                            <th class="px-4 py-2 text-right text-xs text-gray-500">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($frais->paiements()->latest()->limit(5)->get() as $paiement)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</td>
                            <td class="px-4 py-2 text-right font-medium">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection