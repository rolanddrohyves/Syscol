{{-- resources/views/parent/paiements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes paiements')
@section('page-title', 'Suivi des paiements')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Message si aucun enfant -->
    @if(empty($paiementsParEnfant))
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
        <i class="fas fa-exclamation-triangle text-5xl text-yellow-500 mb-3"></i>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucun enfant associé</h3>
        <p class="text-gray-600">Veuillez contacter l'administration pour associer vos enfants à votre compte.</p>
        <div class="mt-4 p-3 bg-white rounded-lg inline-block text-left">
            <p class="text-sm"><strong>Votre email :</strong> {{ Auth::user()->email }}</p>
            <p class="text-sm"><strong>Votre téléphone :</strong> {{ Auth::user()->telephone }}</p>
        </div>
    </div>
    @else
    <!-- Récapitulatif global -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 text-white">
            <p class="text-blue-100 text-sm">Total des frais</p>
            <p class="text-2xl font-bold">{{ number_format($totalGlobalAttendu, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 text-white">
            <p class="text-green-100 text-sm">Total versé</p>
            <p class="text-2xl font-bold">{{ number_format($totalGlobalPaye, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-6 text-white">
            <p class="text-orange-100 text-sm">Reste à payer</p>
            <p class="text-2xl font-bold">{{ number_format($totalGlobalAttendu - $totalGlobalPaye, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>

    <!-- Liste des enfants -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($paiementsParEnfant as $data)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-all">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $data['enfant']->prenom }} {{ $data['enfant']->nom }}</h3>
                        <p class="text-indigo-100 text-sm">{{ $data['enfant']->classe->nom ?? 'Classe non définie' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Cartes de l'enfant -->
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-blue-600">Total frais</p>
                        <p class="text-lg font-bold text-blue-700">{{ number_format($data['total_frais'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-green-600">Déjà payé</p>
                        <p class="text-lg font-bold text-green-700">{{ number_format($data['total_paye'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-3 text-center">
                        <p class="text-xs text-orange-600">Reste à payer</p>
                        <p class="text-lg font-bold {{ $data['reste_a_payer'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($data['reste_a_payer'], 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                </div>

                <!-- Barre de progression -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Taux de paiement</span>
                        <span class="font-bold">{{ $data['taux_paiement'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $data['taux_paiement'] }}%"></div>
                    </div>
                </div>

                <!-- Derniers paiements -->
                @if(isset($data['paiements']) && $data['paiements']->count() > 0)
                <div class="border-t pt-3 mt-3">
                    <p class="text-sm text-gray-500 mb-2">📋 Derniers paiements</p>
                    <div class="space-y-1">
                        @foreach($data['paiements']->take(3) as $paiement)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $paiement->date_paiement->format('d/m/Y') }}</span>
                            <span class="font-medium text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Bouton voir détails -->
                <div class="mt-4 pt-3 border-t">
                    <a href="{{ route('parent.paiements.enfant', $data['enfant']->id) }}" 
                       class="block text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>Voir tous les paiements
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection