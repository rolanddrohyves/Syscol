{{-- resources/views/comptable/paiements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des paiements - SYSCOL')
@section('page-title', 'Gestion des paiements')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-money-bill-wave text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Paiements</h2>
                <p class="text-sm text-gray-500">Gestion des paiements des frais de scolarité</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('comptable.paiements.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouveau paiement
            </a>
            <a href="{{ route('comptable.paiements.export') }}?{{ http_build_query(request()->all()) }}" 
               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total paiements</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'] ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-coins text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ce mois</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['mois'] ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Nombre de paiements</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['nombre'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Moyenne</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['moyenne'] ?? 0, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('comptable.paiements.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Tous les statuts</option>
                    <option value="paye" {{ request('statut') == 'paye' ? 'selected' : '' }}>Payé</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mode</label>
                <select name="mode" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Tous les modes</option>
                    <option value="especes" {{ request('mode') == 'especes' ? 'selected' : '' }}>Espèces</option>
                    <option value="cheque" {{ request('mode') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                    <option value="virement" {{ request('mode') == 'virement' ? 'selected' : '' }}>Virement</option>
                    <option value="carte" {{ request('mode') == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div class="flex items-end space-x-2 md:col-span-5">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('comptable.paiements.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des paiements -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frais</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($paiements as $paiement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $paiement->date_paiement->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center mr-3 text-white text-sm font-semibold">
                                    {{ substr($paiement->eleve->prenom, 0, 1) }}{{ substr($paiement->eleve->nom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $paiement->eleve->prenom }} {{ $paiement->eleve->nom }}</p>
                                    <p class="text-xs text-gray-500">Mat: {{ $paiement->eleve->matricule }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $paiement->eleve->classe->nom }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $paiement->frais->libelle }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($paiement->mode_paiement == 'especes') bg-green-100 text-green-800
                                @elseif($paiement->mode_paiement == 'cheque') bg-blue-100 text-blue-800
                                @elseif($paiement->mode_paiement == 'virement') bg-purple-100 text-purple-800
                                @elseif($paiement->mode_paiement == 'carte') bg-indigo-100 text-indigo-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($paiement->mode_paiement) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($paiement->statut == 'paye') bg-green-100 text-green-800
                                @elseif($paiement->statut == 'en_attente') bg-yellow-100 text-yellow-800
                                @elseif($paiement->statut == 'annule') bg-red-100 text-red-800
                                @endif">
                                @if($paiement->statut == 'paye') Payé
                                @elseif($paiement->statut == 'en_attente') En attente
                                @elseif($paiement->statut == 'annule') Annulé
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('comptable.paiements.show', $paiement->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('comptable.paiements.edit', $paiement->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="printRecu({{ $paiement->id }})" 
                                        class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100"
                                        title="Imprimer reçu">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button onclick="deletePaiement({{ $paiement->id }})" 
                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-money-bill-wave text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun paiement</h3>
                                <p class="text-gray-500 mb-4">Aucun paiement enregistré pour le moment</p>
                                <a href="{{ route('comptable.paiements.create') }}" 
                                   class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter un paiement
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($paiements->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $paiements->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Résumé des paiements par mode -->
    @if($paiements->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-green-600 mr-2"></i>
            Répartition par mode de paiement
        </h3>
        
        @php
            $modes = [
                'especes' => ['label' => 'Espèces', 'color' => 'green', 'total' => 0],
                'cheque' => ['label' => 'Chèque', 'color' => 'blue', 'total' => 0],
                'virement' => ['label' => 'Virement', 'color' => 'purple', 'total' => 0],
                'carte' => ['label' => 'Carte', 'color' => 'indigo', 'total' => 0],
            ];
            
            foreach($paiements as $paiement) {
                if (isset($modes[$paiement->mode_paiement])) {
                    $modes[$paiement->mode_paiement]['total'] += $paiement->montant;
                }
            }
            
            $totalGeneral = array_sum(array_column($modes, 'total'));
        @endphp
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach($modes as $key => $mode)
                @php
                    $pourcentage = $totalGeneral > 0 ? round(($mode['total'] / $totalGeneral) * 100, 1) : 0;
                @endphp
                <div class="text-center p-4 bg-{{ $mode['color'] }}-50 rounded-xl">
                    <span class="text-2xl font-bold text-{{ $mode['color'] }}-600">{{ number_format($mode['total'], 0, ',', ' ') }} FCFA</span>
                    <p class="text-xs text-gray-600 mt-1">{{ $mode['label'] }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="h-1.5 rounded-full bg-{{ $mode['color'] }}-500" style="width: {{ $pourcentage }}%"></div>
                    </div>
                    <p class="text-xs text-{{ $mode['color'] }}-500 mt-1">{{ $pourcentage }}%</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Modal de suppression -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce paiement ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button id="confirmDelete" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour la suppression -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    let deleteId = null;

    function deletePaiement(id) {
        deleteId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteId = null;
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            const form = document.getElementById('deleteForm');
            form.action = `/comptable/paiements/${deleteId}`;
            form.submit();
        }
    });

    function printRecu(id) {
        window.open(`/comptable/paiements/${id}/recu`, '_blank');
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
            closeDeleteModal();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>
@endpush
@endsection