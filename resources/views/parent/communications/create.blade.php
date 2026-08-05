{{-- resources/views/parent/communications/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouveau message - Parent')
@section('page-title', 'Nouveau message')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-pen-alt mr-2"></i>
                    Rédiger un nouveau message
                </h3>
                <a href="{{ route('parent.communications.index') }}" 
                   class="text-white hover:text-indigo-200 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>
        
        <form action="{{ route('parent.communications.send') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Destinataire -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Destinataire <span class="text-red-500">*</span>
                    </label>
                    <select name="destinataire" id="destinataireSelect" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('destinataire') border-red-500 @enderror">
                        <option value="">-- Sélectionner un destinataire --</option>
                        <option value="administration" {{ old('destinataire') == 'administration' ? 'selected' : '' }}>
                            📋 Administration
                        </option>
                        <option value="directeur_etudes" {{ old('destinataire') == 'directeur_etudes' ? 'selected' : '' }}>
                            🎓 Directeur des études
                        </option>
                        <option value="cpe" {{ old('destinataire') == 'cpe' ? 'selected' : '' }}>
                            👮 CPE
                        </option>
                        <option value="professeur_principal" {{ old('destinataire') == 'professeur_principal' ? 'selected' : '' }}>
                            👨‍🏫 Professeur principal
                        </option>
                        <option value="enseignant" {{ old('destinataire') == 'enseignant' ? 'selected' : '' }}>
                            👨‍🏫 Enseignant spécifique
                        </option>
                        <option value="autre" {{ old('destinataire') == 'autre' ? 'selected' : '' }}>
                            📧 Autre destinataire
                        </option>
                    </select>
                    @error('destinataire')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Sélection de l'enseignant (caché par défaut) -->
                <div id="enseignantContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Choisir un enseignant <span class="text-red-500">*</span>
                    </label>
                    <select name="enseignant_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Choisir un enseignant --</option>
                        @foreach($destinataires['enseignants'] ?? [] as $enseignant)
                            <option value="{{ $enseignant->id }}" {{ old('enseignant_id') == $enseignant->id ? 'selected' : '' }}>
                                {{ $enseignant->name }} - {{ $enseignant->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Autre destinataire (caché par défaut) -->
                <div id="autreContainer" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email du destinataire <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="destinataire_nom" 
                           value="{{ old('destinataire_nom') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="exemple@email.com">
                    <p class="mt-1 text-xs text-gray-500">Entrez l'adresse email du destinataire</p>
                </div>
                
                <!-- Enfant concerné -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Enfant concerné <span class="text-gray-400 text-xs">(Optionnel)</span>
                    </label>
                    <select name="eleve_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Non spécifié --</option>
                        @foreach($enfants as $enfant)
                            <option value="{{ $enfant->id }}" {{ old('eleve_id') == $enfant->id ? 'selected' : '' }}>
                                {{ $enfant->prenom }} {{ $enfant->nom }} - {{ $enfant->classe->nom ?? 'Classe non définie' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sujet -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sujet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="sujet" 
                           value="{{ old('sujet') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('sujet') border-red-500 @enderror"
                           placeholder="Objet de votre message">
                    @error('sujet')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" rows="8" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('message') border-red-500 @enderror"
                              placeholder="Écrivez votre message ici...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimum 10 caractères</p>
                </div>
                
                <!-- Boutons d'action -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('parent.communications.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Envoyer le message
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Informations supplémentaires -->
    <div class="mt-6 bg-blue-50 rounded-xl p-4 border border-blue-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">Informations importantes</h4>
                <ul class="mt-1 text-sm text-blue-700 space-y-1">
                    <li>• Les messages sont envoyés directement aux destinataires concernés</li>
                    <li>• Vous recevrez une notification par email en cas de réponse</li>
                    <li>• Pour les urgences, veuillez contacter directement l'établissement par téléphone</li>
                    <li>• Les messages sont conservés dans votre messagerie pendant toute l'année scolaire</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion de l'affichage des destinataires
const destinataireSelect = document.getElementById('destinataireSelect');
const enseignantContainer = document.getElementById('enseignantContainer');
const autreContainer = document.getElementById('autreContainer');

function toggleDestinataireFields() {
    const value = destinataireSelect.value;
    
    // Cacher tous les conteneurs
    if (enseignantContainer) enseignantContainer.classList.add('hidden');
    if (autreContainer) autreContainer.classList.add('hidden');
    
    // Afficher le conteneur approprié
    if (value === 'enseignant' && enseignantContainer) {
        enseignantContainer.classList.remove('hidden');
        // Rendre le champ requis
        if (enseignantContainer.querySelector('select')) {
            enseignantContainer.querySelector('select').required = true;
        }
    } else if (value === 'autre' && autreContainer) {
        autreContainer.classList.remove('hidden');
        // Rendre le champ requis
        if (autreContainer.querySelector('input')) {
            autreContainer.querySelector('input').required = true;
        }
    } else {
        // Désactiver les champs requis
        if (enseignantContainer && enseignantContainer.querySelector('select')) {
            enseignantContainer.querySelector('select').required = false;
        }
        if (autreContainer && autreContainer.querySelector('input')) {
            autreContainer.querySelector('input').required = false;
        }
    }
}

// Écouter les changements
if (destinataireSelect) {
    destinataireSelect.addEventListener('change', toggleDestinataireFields);
    // Appeler au chargement pour initialiser
    toggleDestinataireFields();
}
</script>
@endsection