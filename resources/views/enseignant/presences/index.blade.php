{{-- resources/views/enseignant/presences/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des présences - Enseignant')
@section('page-title', 'Gestion des présences')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtres -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('enseignant.presences') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Classe</label>
                <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Sélectionner une classe</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classeId == $c->id ? 'selected' : '' }}>
                            {{ $c->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Charger
                </button>
            </div>
        </form>
    </div>

    @if($classe)
    <!-- Formulaire de présence -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-check-circle mr-2"></i>
                        Appel de la classe - {{ $classe->nom }}
                    </h3>
                    <p class="text-green-100 text-sm">{{ Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="toutPresenter()" class="px-3 py-1 bg-white text-green-600 rounded-lg text-sm hover:bg-gray-100">
                        Tout présenter
                    </button>
                    <button type="button" onclick="toutAbsent()" class="px-3 py-1 bg-white text-red-600 rounded-lg text-sm hover:bg-gray-100">
                        Tout absenter
                    </button>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('enseignant.presences.marquer') }}" id="presenceForm">
            @csrf
            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Élève</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif (si absent/retard)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($eleves as $index => $eleve)
                        @php
                            $presenceExistante = $presencesExistantes[$eleve->id] ?? null;
                            $statutActuel = $presenceExistante ? $presenceExistante->statut : 'present';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-mono">{{ $eleve->matricule ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center mr-2 text-white text-xs font-semibold">
                                        {{ substr($eleve->prenom, 0, 1) }}{{ substr($eleve->nom, 0, 1) }}
                                    </div>
                                    <span class="font-medium">{{ $eleve->prenom }} {{ $eleve->nom }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <input type="hidden" name="presences[{{ $index }}][eleve_id]" value="{{ $eleve->id }}">
                                <div class="flex items-center justify-center space-x-3">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="presences[{{ $index }}][statut]" value="present" 
                                               class="form-radio text-green-600" {{ $statutActuel == 'present' ? 'checked' : '' }}>
                                        <span class="ml-1 text-sm text-green-600">Présent</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="presences[{{ $index }}][statut]" value="absent" 
                                               class="form-radio text-red-600" {{ $statutActuel == 'absent' ? 'checked' : '' }}>
                                        <span class="ml-1 text-sm text-red-600">Absent</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="presences[{{ $index }}][statut]" value="retard" 
                                               class="form-radio text-orange-600" {{ $statutActuel == 'retard' ? 'checked' : '' }}>
                                        <span class="ml-1 text-sm text-orange-600">Retard</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="presences[{{ $index }}][statut]" value="excuse" 
                                               class="form-radio text-blue-600" {{ $statutActuel == 'excuse' ? 'checked' : '' }}>
                                        <span class="ml-1 text-sm text-blue-600">Excusé</span>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="presences[{{ $index }}][motif]" value="{{ $presenceExistante->motif ?? '' }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                       placeholder="Motif...">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-2 text-gray-400"></i>
                                <p>Aucun élève dans cette classe</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('enseignant.presences') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i>Enregistrer les présences
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-4xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Sélectionnez une classe</h3>
        <p class="text-gray-500">Choisissez une classe pour faire l'appel.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
function toutPresenter() {
    const radios = document.querySelectorAll('input[type="radio"][value="present"]');
    radios.forEach(radio => radio.checked = true);
}

function toutAbsent() {
    const radios = document.querySelectorAll('input[type="radio"][value="absent"]');
    radios.forEach(radio => radio.checked = true);
}
</script>
@endpush
@endsection