{{-- resources/views/eleve/absences.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes absences - Élève')
@section('page-title', 'Mes absences et retards')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    @if(isset($error))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
        {{ $error }}
    </div>
    @endif

    @if(isset($eleve))
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Total absences</p>
                    <p class="text-2xl font-bold">{{ $stats['total_absences'] ?? 0 }}</p>
                </div>
                <i class="fas fa-calendar-times text-2xl text-red-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Non justifiées</p>
                    <p class="text-2xl font-bold">{{ $stats['absences_non_justifiees'] ?? 0 }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-2xl text-orange-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Retards</p>
                    <p class="text-2xl font-bold">{{ $stats['total_retards'] ?? 0 }}</p>
                </div>
                <i class="fas fa-clock text-2xl text-yellow-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Taux d'assiduité</p>
                    <p class="text-2xl font-bold">{{ $stats['taux_assiduite'] ?? 100 }}%</p>
                </div>
                <i class="fas fa-chart-line text-2xl text-green-200"></i>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-filter mr-2 text-indigo-600"></i>Filtres
            </h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('eleve.absences') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="all" {{ (request()->get('type', 'all') == 'all') ? 'selected' : '' }}>Tous</option>
                        <option value="absence" {{ request()->get('type') == 'absence' ? 'selected' : '' }}>Absences</option>
                        <option value="retard" {{ request()->get('type') == 'retard' ? 'selected' : '' }}>Retards</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statut" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="all" {{ (request()->get('statut', 'all') == 'all') ? 'selected' : '' }}>Tous</option>
                        <option value="justifiee" {{ request()->get('statut') == 'justifiee' ? 'selected' : '' }}>Justifiées</option>
                        <option value="non_justifiee" {{ request()->get('statut') == 'non_justifiee' ? 'selected' : '' }}>Non justifiées</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                    <input type="date" name="date_debut" value="{{ request()->get('date_debut') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                    <input type="date" name="date_fin" value="{{ request()->get('date_fin') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                </div>
                <div>
                    <a href="{{ route('eleve.absences') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        <i class="fas fa-undo mr-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des absences -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-list mr-2"></i>Historique des absences et retards
            </h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($absences as $absence)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            @if($absence->type == 'absence')
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-calendar-times mr-1"></i>Absence
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Retard
                                </span>
                            @endif
                            
                            @if($absence->justifiee)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Justifiée
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-clock mr-1"></i>Non justifiée
                                </span>
                            @endif
                        </div>
                        
                        <p class="font-medium text-gray-800">
                            Date: {{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}
                        </p>
                        
                        @if($absence->heure_debut && $absence->heure_fin)
                        <p class="text-sm text-gray-600">
                            Horaire: {{ \Carbon\Carbon::parse($absence->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($absence->heure_fin)->format('H:i') }}
                        </p>
                        @endif
                        
                        @if($absence->motif)
                        <p class="text-sm text-gray-500 mt-1">
                            Motif: {{ $absence->motif }}
                        </p>
                        @endif
                        
                        @if($absence->justification)
                        <div class="mt-2 p-2 bg-blue-50 rounded-lg">
                            <p class="text-xs text-blue-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Justification: {{ $absence->justification }}
                            </p>
                            @if($absence->date_justification)
                            <p class="text-xs text-blue-500 mt-1">
                                Justifiée le: {{ \Carbon\Carbon::parse($absence->date_justification)->format('d/m/Y H:i') }}
                            </p>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <i class="fas fa-calendar-check text-5xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Aucune absence ou retard enregistré</p>
                <p class="text-gray-400 text-sm mt-2">Vous êtes assidu(e) !</p>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if(isset($absences) && method_exists($absences, 'links'))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $absences->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Message d'alerte si trop d'absences -->
    @if(($stats['absences_non_justifiees'] ?? 0) > 5)
    <div class="mt-6 bg-red-50 rounded-xl p-4 border border-red-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-red-800">Attention</h4>
                <p class="mt-1 text-sm text-red-700">
                    Vous avez {{ $stats['absences_non_justifiees'] }} absence(s) non justifiée(s). 
                    Pensez à fournir un justificatif à l'administration.
                </p>
            </div>
        </div>
    </div>
    @endif
    @else
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-12 text-center">
            <i class="fas fa-user-graduate text-5xl text-gray-300 mb-3 block"></i>
            <p class="text-gray-500">Profil élève non trouvé.</p>
            <p class="text-gray-400 text-sm mt-2">Veuillez contacter l'administrateur.</p>
        </div>
    </div>
    @endif
</div>
@endsection