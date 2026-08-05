{{-- resources/views/admin/etablissements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Établissements - SYSCOL')
@section('page-title', 'Gestion des établissements')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-school text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Établissements</h2>
                <p class="text-sm text-gray-500">Gérez tous les établissements scolaires</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.etablissements.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <i class="fas fa-plus mr-2"></i>
                Nouvel établissement
            </a>
            <button onclick="exportData()" 
                    class="flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-300 shadow-sm hover:shadow">
                <i class="fas fa-download mr-2"></i>
                Exporter
            </button>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-indigo-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in" style="animation-delay: 0.1s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-6">
                    <i class="fas fa-school text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in" style="animation-delay: 0.2s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Actifs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['actifs'] }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-6">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in" style="animation-delay: 0.3s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Inactifs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['inactifs'] }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-6">
                    <i class="fas fa-times-circle text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in" style="animation-delay: 0.4s;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Types</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ count($stats['types']) }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform group-hover:rotate-6">
                    <i class="fas fa-layer-group text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative">
                    <input type="text" 
                           placeholder="Rechercher un établissement..." 
                           id="searchInput"
                           class="w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                
                <select id="typeFilter" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les types</option>
                    @foreach($stats['types'] as $type)
                        <option value="{{ $type->type }}">{{ $type->type }}</option>
                    @endforeach
                </select>
                
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="1">Actifs</option>
                    <option value="0">Inactifs</option>
                </select>
            </div>
            
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Vue:</span>
                <button onclick="changeView('grid')" class="p-2 bg-gray-100 rounded-lg hover:bg-indigo-100 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-grid-2"></i>
                </button>
                <button onclick="changeView('list')" class="p-2 bg-gray-100 rounded-lg hover:bg-indigo-100 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Liste des établissements (Vue grille) -->
    <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($etablissements as $etablissement)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="relative h-32 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl overflow-hidden">
                @if($etablissement->logo)
                    <img src="{{ Storage::url($etablissement->logo) }}" alt="{{ $etablissement->nom }}" class="w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-school text-6xl text-white/20"></i>
                    </div>
                @endif
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $etablissement->is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                        {{ $etablissement->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-indigo-600 transition-colors">
                    {{ $etablissement->nom }}
                </h3>
                
                <p class="text-sm text-gray-500 mb-3 flex items-center">
                    <i class="fas fa-tag w-4 text-indigo-500 mr-2"></i>
                    {{ $etablissement->type }} · {{ $etablissement->code_etablissement }}
                </p>
                
                <p class="text-sm text-gray-600 mb-4 flex items-start">
                    <i class="fas fa-map-marker-alt w-4 text-indigo-500 mr-2 mt-1"></i>
                    <span>{{ $etablissement->adresse }}</span>
                </p>
                
                <div class="flex items-center justify-between text-sm text-gray-600 mb-4 pb-4 border-b border-gray-100">
                    <div class="flex items-center">
                        <i class="fas fa-door-open text-indigo-500 mr-1"></i>
                        <span>{{ $etablissement->classes_count ?? 0 }} classes</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-users text-green-500 mr-1"></i>
                        <span>{{ $etablissement->users_count ?? 0 }} utilisateurs</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.etablissements.show', $etablissement->id) }}" 
                           class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors"
                           title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.etablissements.edit', $etablissement->id) }}" 
                           class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                           title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="toggleStatus({{ $etablissement->id }})" 
                                class="p-2 {{ $etablissement->is_active ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }} rounded-lg transition-colors"
                                title="{{ $etablissement->is_active ? 'Désactiver' : 'Activer' }}">
                            <i class="fas {{ $etablissement->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                        </button>
                    </div>
                    
                    <button onclick="deleteEtablissement({{ $etablissement->id }})" 
                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                            title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-school text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun établissement</h3>
            <p class="text-gray-500 mb-6">Commencez par créer votre premier établissement</p>
            <a href="{{ route('admin.etablissements.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all">
                <i class="fas fa-plus mr-2"></i>
                Créer un établissement
            </a>
        </div>
        @endforelse
    </div>

    <!-- Vue liste (cachée par défaut) -->
    <div id="listView" class="hidden">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Établissement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistiques</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($etablissements as $etablissement)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-school text-white"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $etablissement->nom }}</div>
                                    <div class="text-sm text-gray-500">{{ $etablissement->code_etablissement }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $etablissement->type }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ $etablissement->email }}</div>
                            <div class="text-sm text-gray-500">{{ $etablissement->telephone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ $etablissement->classes_count ?? 0 }} classes</div>
                            <div class="text-sm text-gray-500">{{ $etablissement->users_count ?? 0 }} utilisateurs</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $etablissement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $etablissement->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.etablissements.show', $etablissement->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.etablissements.edit', $etablissement->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="deleteEtablissement({{ $etablissement->id }})" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $etablissements->links() }}
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
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer cet établissement ? Cette action est irréversible.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <button id="confirmDelete" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let deleteId = null;

    // Fonctions pour la vue
    function changeView(view) {
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        
        if (view === 'grid') {
            gridView.classList.remove('hidden');
            listView.classList.add('hidden');
        } else {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
        }
    }

    // Filtres
    document.getElementById('searchInput').addEventListener('input', filterEtablissements);
    document.getElementById('typeFilter').addEventListener('change', filterEtablissements);
    document.getElementById('statusFilter').addEventListener('change', filterEtablissements);

    function filterEtablissements() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const type = document.getElementById('typeFilter').value;
        const status = document.getElementById('statusFilter').value;
        
        // Logique de filtrage côté client
        // À implémenter selon vos besoins
    }

    // Suppression
    function deleteEtablissement(id) {
        deleteId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteId = null;
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            // Créer un formulaire pour la suppression
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/etablissements/${deleteId}`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Toggle status
    function toggleStatus(id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/etablissements/${id}/toggle-status`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }

    // Export
    function exportData() {
        window.location.href = '{{ route("admin.etablissements.export") }}?format=csv';
    }

    // Fermer le modal en cliquant dehors
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Animations d'entrée
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('[data-animate="fade-in"]');
        elements.forEach((el, index) => {
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endpush

@push('styles')
<style>
    [data-animate="fade-in"] {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
    }
    
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>
@endpush