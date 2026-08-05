{{-- resources/views/etablissement/eleves/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajouter un élève - SYSCOL')
@section('page-title', 'Ajouter un nouvel élève')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <form method="POST" action="{{ route('etablissement.eleves.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Matricule (auto-généré) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Matricule</label>
                <input type="text" 
                       name="matricule" 
                       value="{{ $matricule }}" 
                       class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-xl text-gray-600"
                       readonly>
                <p class="text-xs text-gray-500 mt-1">Matricule généré automatiquement</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Prénom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="prenom" 
                           value="{{ old('prenom') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('prenom') border-red-500 @enderror"
                           required>
                    @error('prenom')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="nom" 
                           value="{{ old('nom') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('nom') border-red-500 @enderror"
                           required>
                    @error('nom')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de naissance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance <span class="text-red-500">*</span></label>
                    <input type="date" 
                           name="date_naissance" 
                           value="{{ old('date_naissance') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('date_naissance') border-red-500 @enderror"
                           required>
                    @error('date_naissance')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lieu de naissance -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lieu de naissance <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="lieu_naissance" 
                           value="{{ old('lieu_naissance') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('lieu_naissance') border-red-500 @enderror"
                           required>
                    @error('lieu_naissance')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sexe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sexe <span class="text-red-500">*</span></label>
                    <select name="sexe" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('sexe') border-red-500 @enderror" required>
                        <option value="">Sélectionnez</option>
                        <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                        <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                    </select>
                    @error('sexe')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Classe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Classe <span class="text-red-500">*</span></label>
                    <select name="classe_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('classe_id') border-red-500 @enderror" required>
                        <option value="">Sélectionnez une classe</option>
                        @foreach($classes as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id', $classeId) == $classe->id ? 'selected' : '' }}>
                                {{ $classe->nom }} (Capacité: {{ $classe->eleves_count ?? $classe->eleves()->count() }}/{{ $classe->capacite }})
                            </option>
                        @endforeach
                    </select>
                    @error('classe_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Adresse -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresse <span class="text-red-500">*</span></label>
                    <textarea name="adresse" 
                              rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('adresse') border-red-500 @enderror"
                              required>{{ old('adresse') }}</textarea>
                    @error('adresse')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informations du parent -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations du parent</h3>
                </div>

                <!-- Nom du parent -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom du parent <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="nom_parent" 
                           value="{{ old('nom_parent') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('nom_parent') border-red-500 @enderror"
                           required>
                    @error('nom_parent')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone du parent -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone du parent <span class="text-red-500">*</span></label>
                    <input type="tel" 
                           name="telephone_parent" 
                           value="{{ old('telephone_parent') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('telephone_parent') border-red-500 @enderror"
                           required>
                    @error('telephone_parent')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email du parent -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email du parent</label>
                    <input type="email" 
                           name="email_parent" 
                           value="{{ old('email_parent') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('email_parent') border-red-500 @enderror">
                    @error('email_parent')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('status') border-red-500 @enderror" required>
                        <option value="actif" {{ old('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="exclu" {{ old('status') == 'exclu' ? 'selected' : '' }}>Exclu</option>
                        <option value="transferé" {{ old('status') == 'transferé' ? 'selected' : '' }}>Transféré</option>
                        <option value="redoublant" {{ old('status') == 'redoublant' ? 'selected' : '' }}>Redoublant</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo</label>
                    <input type="file" 
                           name="photo" 
                           accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 @error('photo') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (max 2Mo)</p>
                    @error('photo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('etablissement.eleves.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection