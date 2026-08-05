{{-- resources/views/enseignant/emploi_temps/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier un cours - Emploi du temps')
@section('page-title', 'Modifier un cours')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Modifier le cours
            </h3>
        </div>
        
        <form method="POST" action="{{ route('enseignant.emploi_temps.update', $cours->id) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Classe *</label>
                    <select name="classe_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('classe_id') border-red-500 @enderror">
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id', $cours->classe_id) == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Matière *</label>
                    <select name="matiere_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('matiere_id') border-red-500 @enderror">
                        <option value="">Sélectionner une matière</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ old('matiere_id', $cours->matiere_id) == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('matiere_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jour *</label>
                    <select name="jour" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('jour') border-red-500 @enderror">
                        <option value="Lundi" {{ old('jour', $cours->jour) == 'Lundi' ? 'selected' : '' }}>Lundi</option>
                        <option value="Mardi" {{ old('jour', $cours->jour) == 'Mardi' ? 'selected' : '' }}>Mardi</option>
                        <option value="Mercredi" {{ old('jour', $cours->jour) == 'Mercredi' ? 'selected' : '' }}>Mercredi</option>
                        <option value="Jeudi" {{ old('jour', $cours->jour) == 'Jeudi' ? 'selected' : '' }}>Jeudi</option>
                        <option value="Vendredi" {{ old('jour', $cours->jour) == 'Vendredi' ? 'selected' : '' }}>Vendredi</option>
                        <option value="Samedi" {{ old('jour', $cours->jour) == 'Samedi' ? 'selected' : '' }}>Samedi</option>
                    </select>
                    @error('jour')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Salle</label>
                    <input type="text" name="salle" value="{{ old('salle', $cours->salle) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ex: Salle 101">
                    @error('salle')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Heure début *</label>
                    <select name="heure_debut" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('heure_debut') border-red-500 @enderror">
                        @foreach($plagesHoraires as $heureKey => $plage)
                            <option value="{{ $heureKey }}" {{ old('heure_debut', $cours->heure_debut) == $heureKey ? 'selected' : '' }}>
                                {{ $plage }}
                            </option>
                        @endforeach
                    </select>
                    @error('heure_debut')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Heure fin *</label>
                    <select name="heure_fin" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('heure_fin') border-red-500 @enderror">
                        @foreach($plagesHoraires as $heureKey => $plage)
                            <option value="{{ $heureKey }}" {{ old('heure_fin', $cours->heure_fin) == $heureKey ? 'selected' : '' }}>
                                {{ $plage }}
                            </option>
                        @endforeach
                    </select>
                    @error('heure_fin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('enseignant.emploi_temps.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-save mr-2"></i>Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection