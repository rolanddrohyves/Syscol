{{-- resources/views/etablissement/classes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer une classe - SYSCOL')
@section('page-title', 'Créer une nouvelle classe')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-door-open text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle classe</h2>
                <p class="text-gray-500">Ajoutez une nouvelle classe à votre établissement</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('etablissement.classes.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Informations générales -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom de la classe -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Nom de la classe <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nom" 
                               value="{{ old('nom') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('nom') border-red-500 @enderror"
                               placeholder="Ex: 6ème A, Terminale S"
                               required>
                        @error('nom')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Niveau -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Niveau <span class="text-red-500">*</span>
                        </label>
                        <select name="niveau" 
                                id="niveau"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('niveau') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez un niveau</option>
                            <option value="Primaire" {{ old('niveau') == 'Primaire' ? 'selected' : '' }}>Primaire</option>
                            <option value="Collège" {{ old('niveau') == 'Collège' ? 'selected' : '' }}>Collège</option>
                            <option value="Lycée" {{ old('niveau') == 'Lycée' ? 'selected' : '' }}>Lycée</option>
                        </select>
                        @error('niveau')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Série (visible seulement pour Lycée) -->
                    <div class="space-y-2" id="serieField" style="{{ old('niveau') == 'Lycée' ? '' : 'display: none;' }}">
                        <label class="block text-sm font-medium text-gray-700">
                            Série
                        </label>
                        <select name="serie" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('serie') border-red-500 @enderror">
                            <option value="">Sélectionnez une série</option>
                            <option value="L" {{ old('serie') == 'L' ? 'selected' : '' }}>Littéraire (L)</option>
                            <option value="S" {{ old('serie') == 'S' ? 'selected' : '' }}>Scientifique (S)</option>
                            <option value="SE" {{ old('serie') == 'SE' ? 'selected' : '' }}>Sciences Expérimentales (SE)</option>
                            <option value="STEG" {{ old('serie') == 'STEG' ? 'selected' : '' }}>Sciences Technologiques (STEG)</option>
                            <option value="STT" {{ old('serie') == 'STT' ? 'selected' : '' }}>Sciences Techniques (STT)</option>
                        </select>
                        @error('serie')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacité -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Capacité <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="capacite" 
                               value="{{ old('capacite', 30) }}"
                               min="10"
                               max="60"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('capacite') border-red-500 @enderror"
                               required>
                        <p class="text-xs text-gray-400">Nombre maximum d'élèves (10-60)</p>
                        @error('capacite')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Année scolaire et professeur principal -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                    Année scolaire et affectation
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Année scolaire -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Année scolaire <span class="text-red-500">*</span>
                        </label>
                        <select name="annee_scolaire_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('annee_scolaire_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez une année</option>
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

                    <!-- Professeur principal -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Professeur principal
                        </label>
                        <select name="professeur_principal_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('professeur_principal_id') border-red-500 @enderror">
                            <option value="">Non assigné</option>
                            @foreach($professeurs as $professeur)
                                <option value="{{ $professeur->id }}" {{ old('professeur_principal_id') == $professeur->id ? 'selected' : '' }}>
                                    {{ $professeur->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('professeur_principal_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('etablissement.classes.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Créer la classe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Afficher/masquer le champ série en fonction du niveau
    document.getElementById('niveau').addEventListener('change', function() {
        const serieField = document.getElementById('serieField');
        if (this.value === 'Lycée') {
            serieField.style.display = 'block';
        } else {
            serieField.style.display = 'none';
            document.querySelector('select[name="serie"]').value = '';
        }
    });

    // Validation côté client
    document.querySelector('form').addEventListener('submit', function(e) {
        const nom = document.querySelector('input[name="nom"]').value;
        const capacite = document.querySelector('input[name="capacite"]').value;
        const niveau = document.querySelector('select[name="niveau"]').value;
        const annee = document.querySelector('select[name="annee_scolaire_id"]').value;

        if (!nom || !capacite || !niveau || !annee) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return;
        }

        if (capacite < 10 || capacite > 60) {
            e.preventDefault();
            alert('La capacité doit être comprise entre 10 et 60 élèves.');
            return;
        }
    });
</script>
@endpush