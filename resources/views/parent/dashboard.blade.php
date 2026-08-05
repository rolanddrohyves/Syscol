{{-- resources/views/parent/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord - Parent')
@section('page-title', 'Tableau de bord parent')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg animate-float">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Bonjour, {{ Auth::user()->name }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $anneeScolaire->libelle ?? 'Année scolaire' }} · 
                    {{ $trimestreActuel->libelle ?? 'Trimestre en cours' }}
                </p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('parent.enfants.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                <i class="fas fa-child mr-2"></i>Mes enfants
            </a>
            <a href="{{ route('parent.paiements.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700">
                <i class="fas fa-money-bill-wave mr-2"></i>Paiements
            </a>
        </div>
    </div>

    <!-- Alertes -->
    @if(isset($alertes) && count($alertes) > 0)
    <div class="space-y-2">
        @foreach($alertes as $alerte)
        <div class="p-4 {{ $alerte['type'] == 'danger' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }} border rounded-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas {{ $alerte['type'] == 'danger' ? 'fa-exclamation-circle text-red-600' : 'fa-exclamation-triangle text-yellow-600' }} mr-3"></i>
                    <span class="text-sm {{ $alerte['type'] == 'danger' ? 'text-red-800' : 'text-yellow-800' }}">{{ $alerte['message'] }}</span>
                </div>
                @if(isset($alerte['lien']) && $alerte['lien'] != '#')
                <a href="{{ $alerte['lien'] }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Voir détails →
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Mes enfants</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_enfants'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-child text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Moyenne générale</p>
                    <p class="text-2xl font-bold {{ $stats['moyenne_generale'] >= 10 ? 'text-green-600' : 'text-orange-600' }}">
                        {{ $stats['moyenne_generale'] }}/20
                    </p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Absences</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['total_absences'] }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-times text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Taux de paiement</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $stats['total_a_payer'] > 0 ? round(($stats['total_paye'] / $stats['total_a_payer']) * 100, 1) : 0 }}%
                    </p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percent text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des enfants -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($enfants as $enfant)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $enfant->prenom }} {{ $enfant->nom }}</h3>
                        <p class="text-indigo-100 text-sm">Classe: {{ $enfant->classe->nom ?? 'Non définie' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-white text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Moyennes par matière -->
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-700 mb-2">📊 Moyennes par matière</h4>
                    <div class="space-y-2">
                        @php $moyennes = $moyennesParEnfant[$enfant->id]['moyennes'] ?? [] @endphp
                        @foreach($moyennes as $m)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $m['matiere'] }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($m['moyenne'] / 20) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-bold {{ $m['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $m['moyenne'] }}/20
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-2 border-t">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold">Moyenne générale</span>
                            <span class="text-lg font-bold {{ $moyennesParEnfant[$enfant->id]['generale'] >= 10 ? 'text-green-600' : 'text-orange-600' }}">
                                {{ $moyennesParEnfant[$enfant->id]['generale'] }}/20
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Absences récentes -->
                <div class="mb-4">
                    <h4 class="font-semibold text-gray-700 mb-2">⚠️ Absences récentes</h4>
                    @php $absences = $absencesParEnfant[$enfant->id]['dernieres_absences'] ?? collect() @endphp
                    @if($absences->count() > 0)
                        <div class="space-y-1">
                            @foreach($absences as $absence)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $absence->date->format('d/m/Y') }}</span>
                                <span class="{{ $absence->justifiee ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $absence->justifiee ? 'Justifiée' : 'Non justifiée' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucune absence récente</p>
                    @endif
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-2 pt-2 border-t">
                    <a href="{{ route('parent.notes.enfant', $enfant->id) }}" 
                       class="flex-1 text-center px-3 py-2 bg-indigo-100 text-indigo-700 rounded-lg text-sm hover:bg-indigo-200">
                        <i class="fas fa-star mr-1"></i> Notes
                    </a>
                    <a href="{{ route('parent.emploi_temps.enfant', $enfant->id) }}" 
                       class="flex-1 text-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200">
                        <i class="fas fa-calendar-alt mr-1"></i> Emploi du temps
                    </a>
                    <a href="{{ route('parent.absences.enfant', $enfant->id) }}" 
                       class="flex-1 text-center px-3 py-2 bg-orange-100 text-orange-700 rounded-lg text-sm hover:bg-orange-200">
                        <i class="fas fa-calendar-times mr-1"></i> Absences
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Événements récents -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-clock text-indigo-500 mr-2"></i>
                Événements récents
            </h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($evenementsRecents as $event)
            <div class="px-6 py-3 hover:bg-gray-50 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-{{ $event->color }}-100 flex items-center justify-center">
                        <i class="fas {{ $event->icon }} text-{{ $event->color }}-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-800">{{ $event->message }}</p>
                        <p class="text-xs text-gray-400">{{ $event->date->diffForHumans() }}</p>
                    </div>
                </div>
                @if(isset($event->lien))
                <a href="{{ $event->lien }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                    Voir <i class="fas fa-arrow-right ml-1"></i>
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection