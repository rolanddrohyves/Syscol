{{-- resources/views/enseignant/matieres/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mes matières - Enseignant')
@section('page-title', 'Mes matières')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Mes matières enseignées</h2>
                <p class="text-gray-500">Liste des matières que vous enseignez cette année</p>
            </div>
            <div class="bg-indigo-100 rounded-full px-4 py-2">
                <span class="text-indigo-700 font-semibold">{{ $matieres->count() }} matière(s)</span>
            </div>
        </div>
    </div>

    <!-- Liste des matières -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($matieres as $matiere)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
            <!-- En-tête de la matière -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $matiere->nom }}</h3>
                        <p class="text-indigo-100 text-sm">
                            Code: {{ $matiere->code ?? 'N/A' }} | Coef: {{ $matiere->coefficient ?? 1 }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book-open text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Corps de la carte -->
            <div class="p-6">
                <!-- Statistiques -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $statistiques[$matiere->id]['total_eleves'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Élèves</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $statistiques[$matiere->id]['total_notes'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Notes saisies</p>
                    </div>
                </div>

                <!-- Moyenne -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Moyenne générale</span>
                        <span class="font-bold {{ ($statistiques[$matiere->id]['moyenne_generale'] ?? 0) >= 10 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $statistiques[$matiere->id]['moyenne_generale'] ?? 0 }}/20
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $pourcentage = (($statistiques[$matiere->id]['moyenne_generale'] ?? 0) / 20) * 100;
                        @endphp
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
                    </div>
                </div>

                <!-- Meilleure note -->
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-500 text-sm">Meilleure note</span>
                    <span class="font-bold text-green-600">{{ $statistiques[$matiere->id]['meilleure_note'] ?? 0 }}/20</span>
                </div>

                <!-- Classes enseignées -->
                @if(!empty($statistiques[$matiere->id]['classes']) && $statistiques[$matiere->id]['classes']->count() > 0)
                <div class="mt-4 pt-3 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-2">Classes enseignées :</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach($statistiques[$matiere->id]['classes'] as $classe)
                        <span class="px-2 py-1 text-xs bg-gray-100 rounded-full">{{ $classe->nom }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Boutons d'action -->
                <div class="flex space-x-2 mt-4 pt-3 border-t border-gray-100">
                    <a href="{{ route('enseignant.matieres.show', $matiere->id) }}" 
                       class="flex-1 text-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-chart-line mr-1"></i> Détails
                    </a>
                    @php
                        $premiereClasse = $statistiques[$matiere->id]['classes']->first() ?? null;
                    @endphp
                    @if($premiereClasse)
                    <a href="{{ route('enseignant.notes', ['classe_id' => $premiereClasse->id, 'matiere_id' => $matiere->id]) }}" 
                       class="flex-1 text-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-star mr-1"></i> Notes
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Aucune matière trouvée</h3>
                <p class="text-gray-500">Vous n'êtes pas encore assigné à des matières.</p>
                <p class="text-gray-400 text-sm mt-2">Contactez l'administrateur pour être affecté à des matières.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection