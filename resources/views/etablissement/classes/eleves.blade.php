{{-- resources/views/etablissement/classes/eleves.blade.php --}}
@extends('layouts.app')

@section('title', 'Élèves de la classe - SYSCOL')
@section('page-title', 'Élèves de la classe : ' . $classe->nom)

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $classe->nom }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $classe->niveau }} · {{ $eleves->total() }} élève(s) inscrit(s)
                </p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('etablissement.eleves.create', ['classe_id' => $classe->id]) }}" 
               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                <i class="fas fa-user-plus mr-2"></i>
                Ajouter un élève
            </a>
            <a href="{{ route('etablissement.classes.show', $classe->id) }}" 
               class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la classe
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total élèves</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Filles</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['filles'] }}</p>
                </div>
                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-female text-pink-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Garçons</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['garcons'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-male text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Taux occupation</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['taux_occupation'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <form method="GET" action="{{ route('etablissement.classes.eleves', $classe->id) }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Rechercher un élève..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">
                    <i class="fas fa-filter mr-2"></i>
                    Filtrer
                </button>
                <a href="{{ route('etablissement.classes.eleves', $classe->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des élèves -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom & Prénom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sexe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date naissance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($eleves as $eleve)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $eleve->matricule }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center mr-3 text-white text-sm font-semibold">
                                {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                                <p class="text-xs text-gray-500">Né(e) le {{ $eleve->date_naissance->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $eleve->sexe == 'F' ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $eleve->sexe == 'F' ? 'Féminin' : 'Masculin' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $eleve->date_naissance->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $eleve->nom_parent }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $eleve->telephone_parent }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $eleve->status == 'actif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($eleve->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('etablissement.eleves.show', $eleve->id) }}" 
                               class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100"
                               title="Voir détails">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('etablissement.eleves.edit', $eleve->id) }}" 
                               class="p-1.5 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100"
                               title="Modifier">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <a href="{{ route('etablissement.absences.create', ['eleve_id' => $eleve->id]) }}" 
                               class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-100"
                               title="Signaler absence">
                                <i class="fas fa-calendar-times text-sm"></i>
                            </a>
                            <a href="{{ route('etablissement.notes.create', ['eleve_id' => $eleve->id]) }}" 
                               class="p-1.5 bg-purple-50 text-purple-600 rounded hover:bg-purple-100"
                               title="Ajouter note">
                                <i class="fas fa-star text-sm"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-user-graduate text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun élève</h3>
                            <p class="text-gray-500 mb-4">Cette classe n'a pas encore d'élèves inscrits</p>
                            <a href="{{ route('etablissement.eleves.create', ['classe_id' => $classe->id]) }}" 
                               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i>
                                Ajouter un élève
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($eleves->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $eleves->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Graphique de répartition -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Répartition par sexe</h3>
        <div class="h-64" id="sexeChart"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique de répartition par sexe
    const ctx = document.getElementById('sexeChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Filles', 'Garçons'],
            datasets: [{
                data: [{{ $stats['filles'] }}, {{ $stats['garcons'] }}],
                backgroundColor: ['#ec4899', '#3b82f6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Confirmation de suppression
    function deleteEleve(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet élève ?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/etablissement/eleves/${id}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush