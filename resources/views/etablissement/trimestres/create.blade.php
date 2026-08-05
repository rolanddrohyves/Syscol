{{-- resources/views/etablissement/trimestres/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajouter un trimestre - SYSCOL')
@section('page-title', 'Ajouter un trimestre manuellement')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-alt text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Ajouter un trimestre</h2>
                <p class="text-gray-500">Créer un trimestre manuellement</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.trimestres.create-manual') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Année scolaire -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Année scolaire <span class="text-red-500">*</span>
                </label>
                <select name="annee_scolaire_id" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('annee_scolaire_id') border-red-500 @enderror"
                        required>
                    <option value="">Sélectionner une année</option>
                    @foreach($anneesScolaires as $annee)
                        <option value="{{ $annee->id }}" {{ old('annee_scolaire_id') == $annee->id ? 'selected' : '' }}>
                            {{ $annee->libelle }} {{ $annee->is_current ? '(En cours)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('annee_scolaire_id')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Numéro du trimestre -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Numéro du trimestre <span class="text-red-500">*</span>
                </label>
                <select name="numero" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('numero') border-red-500 @enderror"
                        required>
                    <option value="">Sélectionner</option>
                    <option value="1" {{ old('numero') == 1 ? 'selected' : '' }}>Trimestre 1</option>
                    <option value="2" {{ old('numero') == 2 ? 'selected' : '' }}>Trimestre 2</option>
                    <option value="3" {{ old('numero') == 3 ? 'selected' : '' }}>Trimestre 3</option>
                </select>
                @error('numero')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Libellé personnalisé -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Libellé <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="libelle" 
                       value="{{ old('libelle') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('libelle') border-red-500 @enderror"
                       placeholder="Ex: Trimestre 1, Premier semestre..."
                       required>
                @error('libelle')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Date de début <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="date_debut" 
                           value="{{ old('date_debut') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date_debut') border-red-500 @enderror"
                           required>
                    @error('date_debut')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Date de fin <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="date_fin" 
                           value="{{ old('date_fin') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date_fin') border-red-500 @enderror"
                           required>
                    @error('date_fin')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Trimestre courant -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" 
                       name="is_current" 
                       id="is_current"
                       value="1"
                       {{ old('is_current') ? 'checked' : '' }}
                       class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                <label for="is_current" class="text-sm font-medium text-gray-700">
                    Marquer comme trimestre en cours
                </label>
            </div>

            <!-- Boutons -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('etablissement.notes.create') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-xl hover:bg-gray-50">
                    Retour
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700">
                    <i class="fas fa-save mr-2"></i>
                    Créer le trimestre
                </button>
            </div>
        </form>
    </div>
</div>
@endsection