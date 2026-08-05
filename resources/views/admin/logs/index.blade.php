{{-- resources/views/admin/logs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Journaux d\'activité - SYSCOL')
@section('page-title', 'Journaux d\'activité')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-history text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Journaux d'activité</h2>
                <p class="text-sm text-gray-500">Historique des actions utilisateurs</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.logs.export') }}?format=csv" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-file-csv mr-2"></i>
                Export CSV
            </a>
            <a href="{{ route('admin.logs.export') }}?format=json" 
               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all">
                <i class="fas fa-file-code mr-2"></i>
                Export JSON
            </a>
            <button onclick="clearLogs()" 
                    class="flex items-center px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all">
                <i class="fas fa-trash-alt mr-2"></i>
                Vider
            </button>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-database text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Aujourd'hui</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['today']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">7 derniers jours</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['week']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">30 derniers jours</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['month']) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
                <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Tous</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Toutes</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-xl hover:bg-amber-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('admin.logs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Graphique d'activité -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Activité (7 derniers jours)</h3>
        <div class="h-64" id="activityChart"></div>
    </div>

    <!-- Liste des logs -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-{{ $log->action_color }}-100 flex items-center justify-center mr-2">
                                    <span class="text-{{ $log->action_color }}-600 text-sm font-semibold">
                                        {{ $log->user ? substr($log->user->name, 0, 1) : 'S' }}
                                    </span>
                                </div>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $log->user->name ?? 'Système' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full bg-{{ $log->action_color }}-100 text-{{ $log->action_color }}-800">
                                <i class="{{ $log->action_icon }} mr-1"></i>
                                {{ $log->action_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $log->ip_address }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.logs.show', $log->id) }}" 
                                   class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors"
                                   title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="deleteLog({{ $log->id }})" 
                                        class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
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
                                    <i class="fas fa-history text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun journal</h3>
                                <p class="text-gray-500">Aucune activité enregistrée pour le moment</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div id="confirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2" id="confirmTitle">Confirmer</h3>
            <p class="text-gray-600 mb-6" id="confirmMessage">Êtes-vous sûr ?</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button id="confirmAction" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let deleteId = null;
    let actionType = null;

    // Graphique d'activité
    fetch('{{ route("admin.logs.chart") }}')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('activityChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.timeline.labels,
                    datasets: [{
                        label: 'Nombre d\'activités',
                        data: data.timeline.data,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Erreur chargement graphique:', error);
        });

    function deleteLog(id) {
        deleteId = id;
        actionType = 'delete';
        document.getElementById('confirmTitle').textContent = 'Supprimer le journal';
        document.getElementById('confirmMessage').textContent = 'Voulez-vous supprimer cette entrée du journal ?';
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    function clearLogs() {
        actionType = 'clear';
        document.getElementById('confirmTitle').textContent = 'Vider tous les journaux';
        document.getElementById('confirmMessage').textContent = 'Voulez-vous supprimer TOUS les journaux ? Cette action est irréversible.';
        document.getElementById('confirmModal').classList.remove('hidden');
    }

    document.getElementById('confirmAction').addEventListener('click', function() {
        const form = document.getElementById('actionForm');
        
        if (actionType === 'delete' && deleteId) {
            form.action = `/admin/logs/${deleteId}`;
            form.submit();
        } else if (actionType === 'clear') {
            form.action = '{{ route("admin.logs.clear") }}';
            form.submit();
        }
    });

    function closeModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        deleteId = null;
        actionType = null;
    }

    // Fermer le modal en cliquant dehors
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Animations
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stat-card');
        cards?.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
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
    
    .stat-card {
        opacity: 0;
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush