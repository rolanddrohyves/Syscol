{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des utilisateurs - SYSCOL')
@section('page-title', 'Gestion des utilisateurs')

@section('content')
<div class="space-y-6">
    <!-- En-tête avec actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Utilisateurs</h2>
                <p class="text-sm text-gray-500">Gérez tous les utilisateurs du système</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.create') }}" 
               class="flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <i class="fas fa-user-plus mr-2"></i>
                Nouvel utilisateur
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
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Actifs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['actifs'] }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-yellow-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Inactifs</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['inactifs'] }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-times-circle text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group" data-animate="fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Nouveaux (7j)</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['nouveaux'] }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-plus text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative">
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Rechercher un utilisateur..." 
                           class="w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                
                <select name="role" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name ?? $role->name }}
                        </option>
                    @endforeach
                </select>
                
                <select name="etablissement" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Tous les établissements</option>
                    @foreach($etablissements as $etab)
                        <option value="{{ $etab->id }}" {{ request('etablissement') == $etab->id ? 'selected' : '' }}>
                            {{ $etab->nom }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                
                <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all">
                    <i class="fas fa-times mr-2"></i>
                    Réinitialiser
                </a>
            </div>
            
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Vue:</span>
                <button type="button" onclick="changeView('grid')" class="p-2 bg-gray-100 rounded-lg hover:bg-green-100 hover:text-green-600 transition-colors">
                    <i class="fas fa-grid-2"></i>
                </button>
                <button type="button" onclick="changeView('list')" class="p-2 bg-gray-100 rounded-lg hover:bg-green-100 hover:text-green-600 transition-colors">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Actions en masse -->
    <div id="bulkActions" class="hidden bg-green-50 rounded-2xl p-4 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium text-gray-700">
                <span id="selectedCount">0</span> utilisateur(s) sélectionné(s)
            </span>
            <select id="bulkAction" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                <option value="">Choisir une action</option>
                <option value="activate">Activer</option>
                <option value="deactivate">Désactiver</option>
                <option value="delete">Supprimer</option>
            </select>
            <button onclick="executeBulkAction()" class="px-4 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                Appliquer
            </button>
        </div>
        <button onclick="clearSelection()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Vue grille -->
    <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($users as $user)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group" data-user-id="{{ $user->id }}">
            <div class="relative h-24 bg-gradient-to-r from-green-600 to-emerald-600 rounded-t-2xl overflow-hidden">
                <div class="absolute inset-0 opacity-20">
                    <i class="fas fa-users text-6xl absolute -right-2 -top-2 text-white"></i>
                </div>
                <div class="absolute top-3 right-3 flex space-x-2">
                    <input type="checkbox" class="user-checkbox w-4 h-4 text-green-600 rounded focus:ring-green-500" value="{{ $user->id }}">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg -mt-10 border-4 border-white">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-green-600 transition-colors">
                            {{ $user->name }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-user-tag w-5 text-green-500 mr-2"></i>
                        <span>{{ $user->role->display_name ?? $user->role->name ?? 'N/A' }}</span>
                    </div>
                    @if($user->etablissement)
                    <div class="flex items-center text-sm">
                        <i class="fas fa-school w-5 text-green-500 mr-2"></i>
                        <span>{{ $user->etablissement->nom }}</span>
                    </div>
                    @endif
                    @if($user->telephone)
                    <div class="flex items-center text-sm">
                        <i class="fas fa-phone w-5 text-green-500 mr-2"></i>
                        <span>{{ $user->telephone }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.users.show', $user->id) }}" 
                           class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                           title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.users.edit', $user->id) }}" 
                           class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-colors"
                           title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($user->id != auth()->id())
                        <button onclick="toggleStatus({{ $user->id }})" 
                                class="p-2 {{ $user->is_active ? 'bg-blue-50 text-blue-600 hover:bg-blue-100' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }} rounded-lg transition-colors"
                                title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                            <i class="fas {{ $user->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                        </button>
                        @endif
                    </div>
                    
                    @if($user->id != auth()->id())
                    <button onclick="resetPassword({{ $user->id }})" 
                            class="p-2 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition-colors"
                            title="Réinitialiser mot de passe">
                        <i class="fas fa-key"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun utilisateur</h3>
            <p class="text-gray-500 mb-6">Commencez par créer votre premier utilisateur</p>
            <a href="{{ route('admin.users.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all">
                <i class="fas fa-user-plus mr-2"></i>
                Créer un utilisateur
            </a>
        </div>
        @endforelse
    </div>

    <!-- Vue liste -->
    <div id="listView" class="hidden">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Établissement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="row-checkbox w-4 h-4 text-green-600 rounded focus:ring-green-500" value="{{ $user->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center text-white font-bold mr-3">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                {{ $user->role->display_name ?? $user->role->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->etablissement->nom ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $user->telephone ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id != auth()->id())
                                <button onclick="toggleStatus({{ $user->id }})" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas {{ $user->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                </button>
                                <button onclick="resetPassword({{ $user->id }})" class="text-purple-600 hover:text-purple-900">
                                    <i class="fas fa-key"></i>
                                </button>
                                @endif
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
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

<!-- Modal de confirmation -->
<div id="confirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2" id="confirmTitle">Confirmer l'action</h3>
            <p class="text-gray-600 mb-6" id="confirmMessage">Êtes-vous sûr de vouloir effectuer cette action ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeConfirmModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annuler
                </button>
                <button id="confirmAction" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour les actions -->
<form id="actionForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let actionId = null;
    let actionType = null;
    let selectedIds = [];

    // Changement de vue
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

    // Sélection multiple
    document.querySelectorAll('.user-checkbox, .row-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    document.getElementById('selectAll')?.addEventListener('change', function(e) {
        document.querySelectorAll('.row-checkbox').forEach(cb => {
            cb.checked = e.target.checked;
        });
        updateBulkActions();
    });

    function updateBulkActions() {
        selectedIds = [];
        document.querySelectorAll('.user-checkbox:checked, .row-checkbox:checked').forEach(cb => {
            selectedIds.push(cb.value);
        });
        
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        
        if (selectedIds.length > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = selectedIds.length;
        } else {
            bulkActions.classList.add('hidden');
        }
    }

    function clearSelection() {
        document.querySelectorAll('.user-checkbox, .row-checkbox').forEach(cb => {
            cb.checked = false;
        });
        updateBulkActions();
    }

    function executeBulkAction() {
        const action = document.getElementById('bulkAction').value;
        if (!action || selectedIds.length === 0) return;

        let message = '';
        switch(action) {
            case 'activate':
                message = 'Voulez-vous activer les utilisateurs sélectionnés ?';
                break;
            case 'deactivate':
                message = 'Voulez-vous désactiver les utilisateurs sélectionnés ?';
                break;
            case 'delete':
                message = 'Voulez-vous supprimer définitivement les utilisateurs sélectionnés ?';
                break;
        }

        showConfirmModal(
            'Confirmer l\'action',
            message,
            () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.users.bulk-action") }}';
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = action;
                
                const idsInput = document.createElement('input');
                idsInput.type = 'hidden';
                idsInput.name = 'ids';
                idsInput.value = JSON.stringify(selectedIds);
                
                form.appendChild(csrf);
                form.appendChild(actionInput);
                form.appendChild(idsInput);
                document.body.appendChild(form);
                form.submit();
            }
        );
    }

    // Actions individuelles
    function toggleStatus(id) {
        showConfirmModal(
            'Confirmer le changement de statut',
            'Voulez-vous changer le statut de cet utilisateur ?',
            () => {
                const form = document.getElementById('actionForm');
                form.action = `/admin/users/${id}/toggle-status`;
                form.submit();
            }
        );
    }

    function resetPassword(id) {
        showConfirmModal(
            'Réinitialiser le mot de passe',
            'Voulez-vous réinitialiser le mot de passe de cet utilisateur ?',
            () => {
                window.location.href = `/admin/users/${id}/reset-password`;
            }
        );
    }

    function deleteUser(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            const form = document.getElementById('actionForm');
            form.action = `/admin/users/${id}`;
            form.submit();
        }
    }

    // Export
    function exportData() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("admin.users.export") }}?' + params.toString() + '&format=csv';
    }

    // Modal
    function showConfirmModal(title, message, callback) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmModal').classList.remove('hidden');
        
        document.getElementById('confirmAction').onclick = function() {
            closeConfirmModal();
            callback();
        };
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
    }

    // Fermer le modal en cliquant dehors
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmModal();
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