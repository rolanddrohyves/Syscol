{{-- resources/views/etablissement/annes-scolaires/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des années scolaires - SYSCOL')
@section('page-title', 'Gestion des années scolaires')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-calendar-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Années scolaires</h2>
                <p class="text-sm text-gray-500">Gestion des années scolaires et trimestres</p>
            </div>
        </div>
        
        <a href="{{ route('etablissement.annes_scolaires.create') }}" 
           class="flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg">
            <i class="fas fa-plus mr-2"></i>
            Nouvelle année scolaire
        </a>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total années</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Année en cours</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['en_cours'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-play-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Terminées</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['terminees'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">À venir</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['a_venir'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('etablissement.annes_scolaires.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Rechercher une année scolaire..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('etablissement.annes_scolaires.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des années scolaires -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date début</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trimestres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($anneesScolaires as $annee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-900">{{ $annee->libelle }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $annee->date_debut->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $annee->date_fin->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                {{ $annee->trimestres_count ?? $annee->trimestres->count() }} trimestre(s)
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($annee->is_current)
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> En cours
                                </span>
                            @elseif($annee->date_fin < now())
                                <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    Terminée
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    À venir
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('etablissement.annes_scolaires.show', $annee->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('etablissement.annes_scolaires.edit', $annee->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$annee->is_current)
                                <button onclick="setCurrent({{ $annee->id }})" 
                                        class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100"
                                        title="Définir comme année en cours">
                                    <i class="fas fa-play-circle"></i>
                                </button>
                                @endif
                                <button onclick="deleteAnnee({{ $annee->id }})" 
                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                        title="Supprimer"
                                        {{ $annee->trimestres->count() > 0 ? 'disabled' : '' }}>
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
                                    <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune année scolaire</h3>
                                <p class="text-gray-500 mb-4">Commencez par ajouter une année scolaire</p>
                                <a href="{{ route('etablissement.annes_scolaires.create') }}" 
                                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter une année
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($anneesScolaires->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $anneesScolaires->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Timeline des années scolaires -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-clock text-indigo-600 mr-2"></i>
            Chronologie des années scolaires
        </h3>
        
        <div class="relative">
            @foreach($anneesScolaires->sortBy('date_debut') as $index => $annee)
                <div class="flex items-start mb-4 last:mb-0">
                    <div class="flex flex-col items-center mr-4">
                        <div class="w-3 h-3 rounded-full {{ $annee->is_current ? 'bg-green-500' : 'bg-indigo-500' }}"></div>
                        @if(!$loop->last)
                            <div class="w-0.5 h-12 bg-gray-300"></div>
                        @endif
                    </div>
                    <div class="flex-1 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-800">{{ $annee->libelle }}</span>
                            @if($annee->is_current)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">En cours</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $annee->date_debut->format('d/m/Y') }} - {{ $annee->date_fin->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal pour définir l'année en cours -->
<div id="currentModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-play-circle text-2xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Définir comme année en cours</h3>
            <p class="text-gray-600 mb-6">Voulez-vous définir cette année comme l'année scolaire en cours ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeCurrentModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button id="confirmCurrent" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Confirmer
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
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cette année scolaire ?</p>
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
    let currentId = null;
    let deleteId = null;

    // Définir année en cours
    function setCurrent(id) {
        currentId = id;
        document.getElementById('currentModal').classList.remove('hidden');
    }

    function closeCurrentModal() {
        document.getElementById('currentModal').classList.add('hidden');
        currentId = null;
    }

    document.getElementById('confirmCurrent')?.addEventListener('click', function() {
        if (currentId) {
            const form = document.getElementById('actionForm');
            form.action = `/etablissement/annes-scolaires/${currentId}/set-current`;
            form.appendChild(createInput('_method', 'POST'));
            form.submit();
        }
    });

    // Suppression
    function deleteAnnee(id) {
        if (event.target.hasAttribute('disabled')) return;
        deleteId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteId = null;
    }

    document.getElementById('confirmDelete')?.addEventListener('click', function() {
        if (deleteId) {
            const form = document.getElementById('actionForm');
            form.action = `/etablissement/annes-scolaires/${deleteId}`;
            form.appendChild(createInput('_method', 'DELETE'));
            form.submit();
        }
    });

    // Helper pour créer les inputs
    function createInput(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
    }

    // Fermer les modals en cliquant dehors
    document.getElementById('currentModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCurrentModal();
    });
    
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('currentModal')?.classList.contains('hidden')) {
                closeCurrentModal();
            }
            if (!document.getElementById('deleteModal')?.classList.contains('hidden')) {
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