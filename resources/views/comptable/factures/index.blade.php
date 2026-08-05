{{-- resources/views/comptable/factures/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Factures - SYSCOL')
@section('page-title', 'Gestion des factures')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-file-invoice text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Factures</h2>
                <p class="text-sm text-gray-500">Gestion des factures des élèves</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('comptable.factures.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle facture
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-500">Total facturé</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Payées</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['payees'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Impayées</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['impayees'], 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500">Nombre</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['nombre'] }}</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="N° facture, élève..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    <option value="">Toutes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    <option value="">Tous</option>
                    <option value="emise" {{ request('statut') == 'emise' ? 'selected' : '' }}>Émise</option>
                    <option value="envoyee" {{ request('statut') == 'envoyee' ? 'selected' : '' }}>Envoyée</option>
                    <option value="payee" {{ request('statut') == 'payee' ? 'selected' : '' }}>Payée</option>
                    <option value="impayee" {{ request('statut') == 'impayee' ? 'selected' : '' }}>Impayée</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
            </div>
            <div class="flex items-end space-x-2 md:col-span-5">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
                <a href="{{ route('comptable.factures.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Facture</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Échéance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($factures as $facture)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-mono text-sm">{{ $facture->numero }}</td>
                    <td class="px-6 py-4">{{ $facture->date_emission->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $facture->eleve->prenom }} {{ $facture->eleve->nom }}</td>
                    <td class="px-6 py-4">{{ $facture->eleve->classe->nom }}</td>
                    <td class="px-6 py-4 font-medium">{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4">{{ $facture->date_echeance->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statutColors = [
                                'emise' => 'bg-gray-100 text-gray-800',
                                'envoyee' => 'bg-blue-100 text-blue-800',
                                'payee' => 'bg-green-100 text-green-800',
                                'impayee' => 'bg-red-100 text-red-800',
                            ];
                            $statutLabels = [
                                'emise' => 'Émise',
                                'envoyee' => 'Envoyée',
                                'payee' => 'Payée',
                                'impayee' => 'Impayée',
                            ];
                        @endphp
                        <span class="px-3 py-1 text-xs rounded-full {{ $statutColors[$facture->statut] ?? 'bg-gray-100' }}">
                            {{ $statutLabels[$facture->statut] ?? $facture->statut }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('comptable.factures.show', $facture->id) }}" 
                               class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($facture->statut != 'payee')
                                <a href="{{ route('comptable.factures.edit', $facture->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="marquerPayee({{ $facture->id }})" 
                                        class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100" title="Marquer payée">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="envoyerEmail({{ $facture->id }})" 
                                        class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100" title="Envoyer par email">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            @endif
                            <a href="{{ route('comptable.factures.pdf', $facture->id) }}" 
                               class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100" title="PDF" target="_blank">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        Aucune facture trouvée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($factures->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $factures->appends(request()->query())->links() }}
        </div>
        @endif
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