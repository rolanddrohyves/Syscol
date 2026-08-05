{{-- resources/views/comptable/depenses/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails dépense - SYSCOL')
@section('page-title', 'Détails de la dépense')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-red-600 to-orange-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-receipt text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $depense->libelle }}</h1>
                        <p class="text-red-100">Enregistrée le {{ $depense->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 bg-white/20 text-white rounded-full text-sm">
                    {{ ucfirst($depense->categorie) }}
                </span>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-8 py-4 bg-gray-50 border-b border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('comptable.depenses.edit', $depense->id) }}" 
               class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                <i class="fas fa-edit mr-2"></i>Modifier
            </a>
            <a href="{{ route('comptable.depenses.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <!-- Corps -->
        <div class="p-8">
            <!-- Informations principales -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-sm text-gray-500 mb-1">Montant</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-sm text-gray-500 mb-1">Date</p>
                    <p class="text-lg font-semibold">{{ $depense->date->format('d/m/Y') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-sm text-gray-500 mb-1">Mode de paiement</p>
                    <p class="text-lg font-semibold">{{ ucfirst($depense->mode_paiement) }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-sm text-gray-500 mb-1">Bénéficiaire</p>
                    <p class="text-lg font-semibold">{{ $depense->beneficiaire ?? 'Non spécifié' }}</p>
                </div>
            </div>

            <!-- Description -->
            @if($depense->description)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                <p class="text-gray-700 bg-gray-50 p-4 rounded-xl">{{ $depense->description }}</p>
            </div>
            @endif

            <!-- Pièce jointe -->
            @if($depense->piece_jointe)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Pièce jointe</h3>
                <div class="bg-gray-50 p-4 rounded-xl flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium">Document justificatif</p>
                            <p class="text-xs text-gray-500">Ajouté le {{ $depense->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ Storage::url($depense->piece_jointe) }}" target="_blank"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </a>
                </div>
            </div>
            @endif

            <!-- Métadonnées -->
            <div class="border-t border-gray-200 pt-4 text-xs text-gray-400">
                <p>Créée le {{ $depense->created_at->format('d/m/Y à H:i') }}</p>
                @if($depense->created_at != $depense->updated_at)
                <p>Modifiée le {{ $depense->updated_at->format('d/m/Y à H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection