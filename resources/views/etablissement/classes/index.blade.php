@extends('layouts.app')

@section('title', 'Gestion des classes - SYSCOL')
@section('page-title', 'Gestion des classes')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-door-open text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Classes</h2>
                <p class="text-sm text-gray-500">Gérez les classes de votre établissement</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('etablissement.classes.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle classe
            </a>
            <a href="{{ route('etablissement.classes.export') }}?format=csv" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-download mr-2"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total classes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total élèves</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_eleves'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Capacité totale</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['capacite_totale'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chair text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Avec PP</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['avec_pp'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('etablissement.classes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Niveau</label>
                <select name="niveau" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les niveaux</option>
                    @foreach($niveaux as $niveau)
                        <option value="{{ $niveau }}" {{ request('niveau') == $niveau ? 'selected' : '' }}>
                            {{ $niveau }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Année scolaire</label>
                <select name="annee_scolaire_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes</option>
                    @foreach($anneesScolaires as $annee)
                        <option value="{{ $annee->id }}" {{ request('annee_scolaire_id') == $annee->id ? 'selected' : '' }}>
                            {{ $annee->libelle }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nom de la classe..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('etablissement.classes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des classes -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Année scolaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effectif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Professeur principal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($classes as $classe)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-door-open text-white"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $classe->nom }}</div>
                                <div class="text-xs text-gray-500">{{ $classe->serie ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $classe->niveau }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $classe->anneeScolaire->libelle }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900 mr-2">{{ $classe->eleves_count }}/{{ $classe->capacite }}</span>
                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                @php
                                    $pourcentage = $classe->capacite > 0 ? ($classe->eleves_count / $classe->capacite) * 100 : 0;
                                @endphp
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $classe->professeurPrincipal->name ?? 'Non assigné' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('etablissement.classes.show', $classe->id) }}" 
                               class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                               title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('etablissement.classes.edit', $classe->id) }}" 
                               class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('etablissement.classes.eleves', $classe->id) }}" 
                               class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100"
                               title="Voir les élèves">
                                <i class="fas fa-users"></i>
                            </a>
                            <button onclick="deleteClasse({{ $classe->id }})" 
                                    class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                    title="Supprimer"
                                    {{ $classe->eleves_count > 0 ? 'disabled' : '' }}>
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
                                <i class="fas fa-door-open text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune classe</h3>
                            <p class="text-gray-500 mb-4">Commencez par créer votre première classe</p>
                            <a href="{{ route('etablissement.classes.create') }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>
                                Créer une classe
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $classes->appends(request()->query())->links() }}
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
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cette classe ?</p>
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

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let deleteId = null;

    function deleteClasse(id) {
        if (event.target.hasAttribute('disabled')) return;
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
            form.action = `/etablissement/classes/${deleteId}`;
            form.submit();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endpush