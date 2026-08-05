{{-- resources/views/etablissement/enseignants/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier enseignant - SYSCOL')
@section('page-title', 'Modifier l\'enseignant')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier {{ $enseignant->name }}</h2>
                <p class="text-gray-500">{{ $enseignant->enseignant->matricule }}</p>
            </div>
        </div>

        <form action="{{ route('etablissement.enseignants.update', $enseignant->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Informations personnelles -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-circle text-yellow-600 mr-2"></i>
                    Informations personnelles
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', $enseignant->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $enseignant->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Nouveau mot de passe (optionnel)</label>
                        <input type="password" name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Confirmer mot de passe</label>
                        <input type="password" name="password_confirmation" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $enseignant->telephone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $enseignant->enseignant->adresse) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>
            </div>

            <!-- Informations professionnelles -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-briefcase text-yellow-600 mr-2"></i>
                    Informations professionnelles
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Matricule</label>
                        <input type="text" name="matricule" value="{{ old('matricule', $enseignant->enseignant->matricule) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Spécialité</label>
                        <select name="specialite" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                            @foreach($specialites as $specialite)
                                <option value="{{ $specialite }}" {{ old('specialite', $enseignant->enseignant->specialite) == $specialite ? 'selected' : '' }}>
                                    {{ $specialite }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Date d'embauche</label>
                        <input type="date" name="date_embauche" value="{{ old('date_embauche', $enseignant->enseignant->date_embauche?->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500" required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="is_active" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="1" {{ old('is_active', $enseignant->is_active) ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ old('is_active', $enseignant->is_active) ? '' : 'selected' }}>Inactif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Affectations -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-tasks text-yellow-600 mr-2"></i>
                    Affectations
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Classes (Professeur principal)</label>
                        <select name="classes[]" multiple class="w-full px-4 py-3 border border-gray-300 rounded-xl" size="4">
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" 
                                    {{ collect(old('classes', $enseignant->classes->pluck('id')))->contains($classe->id) ? 'selected' : '' }}>
                                    {{ $classe->nom }} ({{ $classe->niveau }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Matières enseignées</label>
                        <select name="matieres[]" multiple class="w-full px-4 py-3 border border-gray-300 rounded-xl" size="4">
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}" 
                                    {{ collect(old('matieres', $enseignant->enseignant->matieres->pluck('id')))->contains($matiere->id) ? 'selected' : '' }}>
                                    {{ $matiere->nom }} (Coeff. {{ $matiere->coefficient }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('etablissement.enseignants.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection