{{-- resources/views/cpe/sanctions/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des sanctions - CPE')
@section('page-title', 'Gestion des sanctions disciplinaires')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-gavel text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Sanctions disciplinaires</h2>
                <p class="text-sm text-gray-500">Gestion des sanctions des élèves</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('cpe.sanctions.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle sanction
            </a>
            <a href="{{ route('cpe.sanctions.export') }}?{{ http_build_query(request()->all()) }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total sanctions</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gavel text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">En cours</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['en_cours'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Exécutées</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['executees'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ce mois</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['ce_mois'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('cpe.sanctions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tous les types</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $value => $label)
                        <option value="{{ $value }}" {{ request('statut') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="flex items-end space-x-2 md:col-span-5">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('cpe.sanctions.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des sanctions -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durée</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sanctions as $sanction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $sanction->date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center mr-3 text-white text-sm font-semibold">
                                    {{ substr($sanction->eleve->prenom, 0, 1) }}{{ substr($sanction->eleve->nom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $sanction->eleve->prenom }} {{ $sanction->eleve->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $sanction->eleve->classe->nom }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($sanction->type == 'avertissement') bg-yellow-100 text-yellow-800
                                @elseif($sanction->type == 'retenue') bg-orange-100 text-orange-800
                                @elseif($sanction->type == 'exclusion_temporaire') bg-red-100 text-red-800
                                @elseif($sanction->type == 'exclusion_definitive') bg-red-200 text-red-900
                                @elseif($sanction->type == 'travail_extra') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $types[$sanction->type] ?? $sanction->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $sanction->motif }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($sanction->duree)
                                {{ $sanction->duree }} h
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($sanction->statut == 'en_cours') bg-yellow-100 text-yellow-800
                                @elseif($sanction->statut == 'executee') bg-green-100 text-green-800
                                @elseif($sanction->statut == 'annulee') bg-gray-100 text-gray-800
                                @endif">
                                {{ $statuts[$sanction->statut] ?? $sanction->statut }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('cpe.sanctions.show', $sanction->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cpe.sanctions.edit', $sanction->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Menu déroulant pour changer le statut -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100"
                                            title="Changer le statut">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" 
                                         class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-200 z-50">
                                        <form action="{{ route('cpe.sanctions.update-statut', $sanction->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="statut" value="en_cours">
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50 rounded-t-xl">
                                                En cours
                                            </button>
                                        </form>
                                        <form action="{{ route('cpe.sanctions.update-statut', $sanction->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="statut" value="executee">
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                Exécutée
                                            </button>
                                        </form>
                                        <form action="{{ route('cpe.sanctions.update-statut', $sanction->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="statut" value="annulee">
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-b-xl">
                                                Annulée
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <button onclick="deleteSanction({{ $sanction->id }})" 
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
                                    <i class="fas fa-gavel text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune sanction</h3>
                                <p class="text-gray-500 mb-4">Aucune sanction disciplinaire enregistrée</p>
                                <a href="{{ route('cpe.sanctions.create') }}" 
                                   class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter une sanction
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($sanctions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $sanctions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Graphique des sanctions par type -->
    @if($sanctions->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
            Répartition des sanctions par type
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @php
                $totalSanctions = $sanctions->total();
                $counts = [];
                foreach($sanctions as $sanction) {
                    $counts[$sanction->type] = ($counts[$sanction->type] ?? 0) + 1;
                }
                $colors = ['yellow', 'orange', 'red', 'red', 'blue'];
                $index = 0;
            @endphp
            
            @foreach($types as $value => $label)
                @php
                    $count = $counts[$value] ?? 0;
                    $pourcentage = $totalSanctions > 0 ? round(($count / $totalSanctions) * 100) : 0;
                    $color = $colors[$index % count($colors)];
                    $index++;
                @endphp
                <div class="text-center p-4 bg-{{ $color }}-50 rounded-xl">
                    <span class="text-2xl font-bold text-{{ $color }}-600">{{ $count }}</span>
                    <p class="text-xs text-gray-600 mt-1">{{ $label }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="h-1.5 rounded-full bg-{{ $color }}-500" style="width: {{ $pourcentage }}%"></div>
                    </div>
                    <p class="text-xs text-{{ $color }}-500 mt-1">{{ $pourcentage }}%</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cette sanction ?</p>
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

    function deleteSanction(id) {
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
            form.action = `/cpe/sanctions/${deleteId}`;
            form.submit();
        }
    });

    // Fermer le modal en cliquant dehors
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
            closeDeleteModal();
        }
    });

    // Initialiser Alpine.js pour les menus déroulants
    document.addEventListener('alpine:init', () => {
        Alpine.data('dropdown', () => ({
            open: false,
            toggle() {
                this.open = !this.open;
            }
        }));
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