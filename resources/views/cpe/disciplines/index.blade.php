{{-- resources/views/cpe/disciplines/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des disciplines - CPE')
@section('page-title', 'Gestion des incidents disciplinaires')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Incidents disciplinaires</h2>
                <p class="text-sm text-gray-500">Gestion des incidents et comportements des élèves</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('cpe.disciplines.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouvel incident
            </a>
            <a href="{{ route('cpe.disciplines.export') }}?{{ http_build_query(request()->all()) }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total incidents</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Cette semaine</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['cette_semaine'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ce mois</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['ce_mois'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Élèves concernés</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['eleves_distincts'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('cpe.disciplines.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
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
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Tous les types</option>
                    <option value="incident" {{ request('type') == 'incident' ? 'selected' : '' }}>Incident</option>
                    <option value="avertissement" {{ request('type') == 'avertissement' ? 'selected' : '' }}>Avertissement</option>
                    <option value="retenue" {{ request('type') == 'retenue' ? 'selected' : '' }}>Retenue</option>
                    <option value="exclusion" {{ request('type') == 'exclusion' ? 'selected' : '' }}>Exclusion</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gravité</label>
                <select name="gravite" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Toutes</option>
                    <option value="faible" {{ request('gravite') == 'faible' ? 'selected' : '' }}>Faible</option>
                    <option value="moyenne" {{ request('gravite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                    <option value="elevee" {{ request('gravite') == 'elevee' ? 'selected' : '' }}>Élevée</option>
                    <option value="critique" {{ request('gravite') == 'critique' ? 'selected' : '' }}>Critique</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            
            <div class="flex items-end space-x-2 md:col-span-5">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('cpe.disciplines.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des incidents -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gravité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($disciplines as $discipline)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $discipline->date->format('d/m/Y') }}
                            @if($discipline->heure)
                                <span class="text-xs text-gray-400 block">{{ $discipline->heure }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center mr-3 text-white text-sm font-semibold">
                                    {{ substr($discipline->eleve->prenom, 0, 1) }}{{ substr($discipline->eleve->nom, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $discipline->eleve->prenom }} {{ $discipline->eleve->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $discipline->eleve->classe->nom }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($discipline->type == 'incident') bg-red-100 text-red-800
                                @elseif($discipline->type == 'avertissement') bg-yellow-100 text-yellow-800
                                @elseif($discipline->type == 'retenue') bg-orange-100 text-orange-800
                                @elseif($discipline->type == 'exclusion') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($discipline->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full 
                                @if($discipline->gravite == 'faible') bg-green-100 text-green-800
                                @elseif($discipline->gravite == 'moyenne') bg-yellow-100 text-yellow-800
                                @elseif($discipline->gravite == 'elevee') bg-orange-100 text-orange-800
                                @elseif($discipline->gravite == 'critique') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($discipline->gravite) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $discipline->description }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('cpe.disciplines.show', $discipline->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cpe.disciplines.edit', $discipline->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteDiscipline({{ $discipline->id }})" 
                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-exclamation-triangle text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun incident</h3>
                                <p class="text-gray-500 mb-4">Aucun incident disciplinaire enregistré</p>
                                <a href="{{ route('cpe.disciplines.create') }}" 
                                   class="px-4 py-2 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-lg hover:from-red-700 hover:to-orange-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter un incident
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($disciplines->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $disciplines->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Graphique des incidents par type -->
    @if($disciplines->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-pie text-red-600 mr-2"></i>
            Répartition des incidents
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $total = $disciplines->total();
                $counts = [
                    'incident' => $disciplines->where('type', 'incident')->count(),
                    'avertissement' => $disciplines->where('type', 'avertissement')->count(),
                    'retenue' => $disciplines->where('type', 'retenue')->count(),
                    'exclusion' => $disciplines->where('type', 'exclusion')->count(),
                ];
            @endphp
            
            <div class="text-center p-4 bg-red-50 rounded-xl">
                <span class="text-2xl font-bold text-red-600">{{ $counts['incident'] }}</span>
                <p class="text-xs text-gray-600 mt-1">Incidents</p>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="h-1.5 rounded-full bg-red-500" style="width: {{ $total > 0 ? ($counts['incident'] / $total * 100) : 0 }}%"></div>
                </div>
            </div>
            
            <div class="text-center p-4 bg-yellow-50 rounded-xl">
                <span class="text-2xl font-bold text-yellow-600">{{ $counts['avertissement'] }}</span>
                <p class="text-xs text-gray-600 mt-1">Avertissements</p>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="h-1.5 rounded-full bg-yellow-500" style="width: {{ $total > 0 ? ($counts['avertissement'] / $total * 100) : 0 }}%"></div>
                </div>
            </div>
            
            <div class="text-center p-4 bg-orange-50 rounded-xl">
                <span class="text-2xl font-bold text-orange-600">{{ $counts['retenue'] }}</span>
                <p class="text-xs text-gray-600 mt-1">Retenues</p>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="h-1.5 rounded-full bg-orange-500" style="width: {{ $total > 0 ? ($counts['retenue'] / $total * 100) : 0 }}%"></div>
                </div>
            </div>
            
            <div class="text-center p-4 bg-purple-50 rounded-xl">
                <span class="text-2xl font-bold text-purple-600">{{ $counts['exclusion'] }}</span>
                <p class="text-xs text-gray-600 mt-1">Exclusions</p>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="h-1.5 rounded-full bg-purple-500" style="width: {{ $total > 0 ? ($counts['exclusion'] / $total * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Élèves les plus problématiques -->
    @if(($elevesProblematiques ?? collect())->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
            Élèves avec le plus d'incidents
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($elevesProblematiques as $eleve)
            <div class="p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white font-bold">
                        {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                        <p class="text-xs text-gray-500">{{ $eleve->classe->nom }}</p>
                    </div>
                    <div class="ml-auto">
                        <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800 font-semibold">
                            {{ $eleve->incidents_count }}
                        </span>
                    </div>
                </div>
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
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cet incident ?</p>
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

    function deleteDiscipline(id) {
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
            form.action = `/cpe/disciplines/${deleteId}`;
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