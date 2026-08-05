{{-- resources/views/parent/paiements/enfant.blade.php --}}
@extends('layouts.app')

@section('title', 'Paiements - ' . $enfant->prenom . ' ' . $enfant->nom)
@section('page-title', 'Paiements de ' . $enfant->prenom . ' ' . $enfant->nom)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $enfant->prenom }} {{ $enfant->nom }}</h2>
                    <p class="text-indigo-100">{{ $enfant->classe->nom ?? 'Classe non définie' }}</p>
                </div>
                <a href="{{ route('parent.paiements.index') }}" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Retour
                </a>
            </div>
        </div>

        <!-- Cartes récapitulatives -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-blue-600">Total des frais</p>
                    <p class="text-2xl font-bold text-blue-700">{{ number_format($totalFrais, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-green-600">Total versé</p>
                    <p class="text-2xl font-bold text-green-700">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-orange-50 rounded-xl p-4 text-center">
                    <p class="text-sm text-orange-600">Reste à payer</p>
                    <p class="text-2xl font-bold {{ $resteAPayer > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($resteAPayer, 0, ',', ' ') }} FCFA
                    </p>
                </div>
            </div>

            <!-- Barre de progression -->
            <div class="mb-6">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Taux de paiement</span>
                    <span class="font-bold">{{ $totalFrais > 0 ? round(($totalPaye / $totalFrais) * 100, 2) : 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalFrais > 0 ? round(($totalPaye / $totalFrais) * 100, 2) : 0 }}%"></div>
                </div>
            </div>

            <!-- Historique des paiements -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Historique des paiements</h3>
            
            @if($paiements->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type de frais</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($paiements as $paiement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm font-mono">{{ $paiement->reference ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $paiement->frais->libelle ?? 'Frais de scolarité' }}</td>
                            <td class="px-4 py-3 text-right font-medium text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right">Total</td>
                            <td class="px-4 py-3 text-right text-green-700">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-8 bg-gray-50 rounded-xl">
                <i class="fas fa-receipt text-4xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">Aucun paiement enregistré pour cet enfant</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection