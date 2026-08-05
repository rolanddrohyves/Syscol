{{-- resources/views/admin/roles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des rôles - SYSCOL')
@section('page-title', 'Gestion des rôles')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-user-tag text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Rôles</h2>
                <p class="text-sm text-gray-500">Gérez les rôles et permissions des utilisateurs</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.roles.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <i class="fas fa-plus mr-2"></i>
                Nouveau rôle
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
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total rôles</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-tag text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Utilisateurs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['utilisateurs'] }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Moyenne/rôle</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['moyenne_utilisateurs'] }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-bar text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Niveau max</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['niveau_max'] }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-arrow-up text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recherche -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('admin.roles') }}" class="flex flex-col md:flex-row md:items-center gap-4">
            <div class="relative flex-1">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Rechercher un rôle..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                
                <a href="{{ route('admin.roles') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all">
                    <i class="fas fa-times mr-2"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des rôles -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Niveau</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateurs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé le</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($roles as $role)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user-tag text-white"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $role->display_name }}</div>
                                <div class="text-sm text-gray-500">{{ $role->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $role->description ?? 'Aucune description' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                Niveau {{ $role->level }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.roles.users', $role->id) }}" class="text-purple-600 hover:text-purple-900 hover:underline">
                            {{ $role->users_count }} utilisateur(s)
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $role->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.roles.show', $role->id) }}" 
                               class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors"
                               title="Voir détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if(!in_array($role->name, ['super_admin', 'admin_etablissement', 'enseignant', 'eleve', 'parent', 'comptable', 'cpe', 'directeur_etudes']))
                                <a href="{{ route('admin.roles.edit', $role->id) }}" 
                                   class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                                   title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.roles.duplicate', $role->id) }}" 
                                   class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                                   title="Dupliquer">
                                    <i class="fas fa-copy"></i>
                                </a>
                                <button onclick="deleteRole({{ $role->id }})" 
                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                        title="Supprimer"
                                        {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <span class="p-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed" title="Rôle système - non modifiable">
                                    <i class="fas fa-lock"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-user-tag text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun rôle trouvé</h3>
                            <p class="text-gray-500 mb-4">Commencez par créer votre premier rôle</p>
                            <a href="{{ route('admin.roles.create') }}" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all">
                                <i class="fas fa-plus mr-2"></i>
                                Créer un rôle
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $roles->appends(request()->query())->links() }}
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
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce rôle ? Cette action est irréversible.</p>
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

<!-- Formulaire caché pour la suppression -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let deleteId = null;

    function deleteRole(id) {
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
            form.action = `/admin/roles/${deleteId}`;
            form.submit();
        }
    });

    function exportData() {
        window.location.href = '{{ route("admin.roles.export") }}?format=csv';
    }

    // Fermer le modal en cliquant dehors
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Animations
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