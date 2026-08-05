{{-- resources/views/comptable/factures/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails facture - SYSCOL')
@section('page-title', 'Facture ' . $facture->numero)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- En-tête -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-file-invoice text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Facture {{ $facture->numero }}</h1>
                        <p class="text-purple-100">Émise le {{ $facture->date_emission->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div>
                    @php
                        $statutColors = [
                            'emise' => 'bg-gray-500',
                            'envoyee' => 'bg-blue-500',
                            'payee' => 'bg-green-500',
                            'impayee' => 'bg-red-500',
                        ];
                        $statutLabels = [
                            'emise' => 'Émise',
                            'envoyee' => 'Envoyée',
                            'payee' => 'Payée',
                            'impayee' => 'Impayée',
                        ];
                    @endphp
                    <span class="px-4 py-2 {{ $statutColors[$facture->statut] ?? 'bg-gray-500' }} text-white rounded-full text-sm">
                        {{ $statutLabels[$facture->statut] ?? $facture->statut }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-8 py-4 bg-gray-50 border-b border-gray-200 flex justify-end space-x-3">
            @if($facture->statut != 'payee')
                <a href="{{ route('comptable.factures.edit', $facture->id) }}" 
                   class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <button onclick="marquerPayee({{ $facture->id }})" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>Marquer payée
                </button>
                <button onclick="envoyerEmail({{ $facture->id }})" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-envelope mr-2"></i>Envoyer par email
                </button>
            @endif
            <a href="{{ route('comptable.factures.pdf', $facture->id) }}" target="_blank"
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-file-pdf mr-2"></i>Télécharger PDF
            </a>
            <a href="{{ route('comptable.factures.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <!-- Corps de la facture -->
        <div class="p-8">
            <!-- En-tête société -->
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ config('app.name') }}</h2>
                    <p class="text-gray-600">Système de Gestion Scolaire</p>
                    <p class="text-gray-500 text-sm mt-2">N° TVA: FR12345678901</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Date d'échéance</p>
                    <p class="text-lg font-bold {{ $facture->date_echeance->isPast() ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $facture->date_echeance->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <!-- Informations élève -->
            <div class="bg-gray-50 rounded-xl p-6 mb-8">
                <h3 class="text-sm font-medium text-gray-500 mb-2">FACTURÉ À</h3>
                <p class="text-lg font-bold text-gray-800">{{ $facture->eleve->prenom }} {{ $facture->eleve->nom }}</p>
                <p class="text-gray-600">Classe: {{ $facture->eleve->classe->nom }}</p>
                <p class="text-gray-600">Matricule: {{ $facture->eleve->matricule }}</p>
                @if($facture->eleve->email_parent)
                    <p class="text-gray-600">{{ $facture->eleve->email_parent }}</p>
                @endif
            </div>

            <!-- Tableau des montants -->
            <table class="w-full mb-8">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Description</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-gray-500">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-4">
                            <p class="font-medium text-gray-800">{{ $facture->description ?? 'Frais de scolarité' }}</p>
                            <p class="text-xs text-gray-500">Réf: {{ $facture->numero }}</p>
                        </td>
                        <td class="px-4 py-4 text-right font-medium text-gray-800">
                            {{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-4 py-3 text-left font-medium">Sous-total HT</td>
                        <td class="px-4 py-3 text-right">{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-left font-medium">TVA (18%)</td>
                        <td class="px-4 py-3 text-right">{{ number_format($facture->montant_ttc - $facture->montant_ht, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr class="text-lg font-bold">
                        <td class="px-4 py-3 text-left">TOTAL TTC</td>
                        <td class="px-4 py-3 text-right text-purple-600">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Mentions -->
            <div class="border-t border-gray-200 pt-6 text-sm text-gray-500">
                <p class="mb-1">Arrêté la présente facture à la somme de <strong>{{ ucfirst(App\Helpers\NumberToWords::convert($facture->montant_ttc)) }} FCFA</strong></p>
                <p class="mb-1">TVA non applicable, article 257 du CGI</p>
                <p class="mb-1">Escompte pour règlement anticipé : néant</p>
                <p class="mb-1">Pénalités de retard : 3 fois le taux d'intérêt légal</p>
            </div>

            <!-- Pied de page -->
            <div class="mt-8 flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Cachet et signature</p>
                    <p class="text-xs text-gray-400">Document généré le {{ now()->format('d/m/Y H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-800">Pour {{ config('app.name') }}</p>
                    <p class="text-xs text-gray-500">Le comptable</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour les actions -->
<form id="actionForm" method="POST" style="display: none;">@csrf</form>

@push('scripts')
<script>
    function marquerPayee(id) {
        if (confirm('Marquer cette facture comme payée ?')) {
            const form = document.getElementById('actionForm');
            form.action = `/comptable/factures/${id}/payee`;
            form.submit();
        }
    }
    
    function envoyerEmail(id) {
        if (confirm('Envoyer cette facture par email au parent ?')) {
            const form = document.getElementById('actionForm');
            form.action = `/comptable/factures/${id}/email`;
            form.submit();
        }
    }
</script>
@endpush
@endsection