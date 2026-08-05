{{-- resources/views/cpe/retards/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des retards - CPE')
@section('page-title', 'Gestion des retards')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-clock text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Retards</h2>
                <p class="text-sm text-gray-500">Gestion des retards des élèves</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('cpe.retards.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Signaler un retard
            </a>
            <a href="{{ route('cpe.retards.export') }}?{{ http_build_query(request()->all()) }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total retards</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['aujourdhui'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ce mois</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['ce_mois'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('cpe.retards.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('cpe.retards.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des retards -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Heure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($retards as $retard)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            <div>{{ $retard->date->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $retard->heure }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center mr-3 text-white text-sm font-semibold">
                                    {{ substr($retard->eleve->prenom, 0, 1) }}{{ substr($retard->eleve->nom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $retard->eleve->prenom }} {{ $retard->eleve->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $retard->eleve->classe->nom }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $retard->motif ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($retard->justifiee)
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Justifié
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Non justifié
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('cpe.retards.show', $retard->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cpe.retards.edit', $retard->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$retard->justifiee)
                                <button onclick="justifierRetard({{ $retard->id }})" 
                                        class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100"
                                        title="Justifier">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                                <button onclick="deleteRetard({{ $retard->id }})" 
                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-clock text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun retard</h3>
                                <p class="text-gray-500 mb-4">Aucun retard enregistré pour le moment</p>
                                <a href="{{ route('cpe.retards.create') }}" 
                                   class="px-4 py-2 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-lg hover:from-yellow-700 hover:to-orange-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Signaler un retard
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($retards->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $retards->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Graphique des retards par heure (optionnel) -->
    @if($retards->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-bar text-yellow-600 mr-2"></i>
            Répartition des retards par heure
        </h3>
        
        @php
            $heures = ['08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'];
            $comptes = array_fill(0, count($heures), 0);
            
            foreach($retards as $retard) {
                if ($retard->heure) {
                    $h = intval(substr($retard->heure, 0, 2));
                    if ($h >= 8 && $h < 9) $comptes[0]++;
                    elseif ($h >= 9 && $h < 10) $comptes[1]++;
                    elseif ($h >= 10 && $h < 11) $comptes[2]++;
                    elseif ($h >= 11 && $h < 12) $comptes[3]++;
                    elseif ($h >= 14 && $h < 15) $comptes[4]++;
                    elseif ($h >= 15 && $h < 16) $comptes[5]++;
                    elseif ($h >= 16 && $h < 17) $comptes[6]++;
                }
            }
            
            $max = max($comptes) ?: 1;
        @endphp
        
        <div class="space-y-3">
            @foreach($heures as $index => $heure)
                @php $pourcentage = ($comptes[$index] / $max) * 100; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $heure }}</span>
                        <span class="font-medium text-gray-800">{{ $comptes[$index] }} retard(s)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full bg-yellow-500" style="width: {{ $pourcentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Modal de justification -->
<div id="justifyModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Justifier le retard</h3>
            <textarea id="justification" rows="3" 
                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Motif de justification..."></textarea>
            <div class="flex justify-center space-x-3 mt-4">
                <button onclick="closeJustifyModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button onclick="submitJustification()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Justifier
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirmer la suppression</h3>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce retard ?</p>
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

<!-- Formulaire caché pour les actions -->
<form id="actionForm" method="POST" style="display: none;">
    @csrf
</form>
@endsection

@push('scripts')
<script>
    let currentRetardId = null;

    // Justification
    function justifierRetard(id) {
        currentRetardId = id;
        document.getElementById('justifyModal').classList.remove('hidden');
    }

    function closeJustifyModal() {
        document.getElementById('justifyModal').classList.add('hidden');
        document.getElementById('justification').value = '';
        currentRetardId = null;
    }

    function submitJustification() {
        if (currentRetardId) {
            const justification = document.getElementById('justification').value;
            if (!justification) {
                alert('Veuillez saisir un motif de justification');
                return;
            }
            
            const form = document.getElementById('actionForm');
            form.action = `/cpe/retards/${currentRetardId}/justify`;
            
            const justif = document.createElement('input');
            justif.type = 'hidden';
            justif.name = 'justification';
            justif.value = justification;
            
            form.appendChild(justif);
            form.submit();
        }
    }

    // Suppression
    function deleteRetard(id) {
        currentRetardId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        currentRetardId = null;
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (currentRetardId) {
            const form = document.getElementById('actionForm');
            form.action = `/cpe/retards/${currentRetardId}`;
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(method);
            form.submit();
        }
    });

    // Fermer les modals en cliquant dehors
    document.getElementById('justifyModal').addEventListener('click', function(e) {
        if (e.target === this) closeJustifyModal();
    });
    
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('justifyModal').classList.contains('hidden')) {
                closeJustifyModal();
            }
            if (!document.getElementById('deleteModal').classList.contains('hidden')) {
                closeDeleteModal();
            }
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