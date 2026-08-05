{{-- resources/views/etablissement/enseignants/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajouter un enseignant - SYSCOL')
@section('page-title', 'Ajouter un enseignant - ' . Auth::user()->etablissement->nom)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête avec carte établissement -->
        <div class="mb-8 pb-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Nouvel enseignant</h2>
                        <p class="text-gray-500">Ajouter un enseignant à votre établissement</p>
                    </div>
                </div>
            </div>
            
            <!-- Carte d'information de l'établissement -->
            <div class="mt-6 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-school text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Établissement</p>
                            <p class="text-lg font-semibold text-gray-800">{{ Auth::user()->etablissement->nom }}</p>
                            @if(Auth::user()->etablissement->code_etablissement)
                                <p class="text-xs text-gray-500">Code: {{ Auth::user()->etablissement->code_etablissement }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Type</p>
                        <p class="text-md font-medium text-gray-700">{{ Auth::user()->etablissement->type }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ MESSAGES D'ERREUR SPÉCIFIQUES -->
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg animate-pulse">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Erreur de sécurité</h3>
                        <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->has('classes') || $errors->has('classes_enseignees'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">⚠️ Alerte de sécurité</h3>
                        <p class="text-sm text-red-700 mt-1">
                            {{ $errors->first('classes') ?: $errors->first('classes_enseignees') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulaire -->
        <form action="{{ route('etablissement.enseignants.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Champ caché avec l'établissement (sécurisé) -->
            <input type="hidden" name="etablissement_id" value="{{ Auth::user()->etablissement_id }}">

            <!-- ============================================ -->
            <!-- 1. INFORMATIONS PERSONNELLES                -->
            <!-- ============================================ -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-circle text-purple-600 mr-2"></i>
                    Informations personnelles
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom complet -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Nom complet <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                               placeholder="Nom et prénom"
                               required>
                        @error('name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                               placeholder="enseignant@ecole.sn"
                               required>
                        @error('email')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mot de passe avec visibilité -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Mot de passe <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                                   placeholder="••••••••"
                                   required>
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password', 'eyeIcon')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-purple-600">
                                <i id="eyeIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400">Minimum 8 caractères</p>
                        @error('password')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmation mot de passe -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Confirmer le mot de passe <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                   placeholder="••••••••"
                                   required>
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password_confirmation', 'eyeIconConfirm')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-purple-600">
                                <i id="eyeIconConfirm" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Téléphone adaptable -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Téléphone
                        </label>
                        <input type="tel" 
                               name="telephone" 
                               id="telephone"
                               value="{{ old('telephone') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('telephone') border-red-500 @enderror"
                               placeholder="+221 77 123 45 67 ou 771234567">
                        <p class="text-xs text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Format international ou local accepté
                        </p>
                        @error('telephone')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Adresse -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Adresse
                        </label>
                        <input type="text" 
                               name="adresse" 
                               value="{{ old('adresse') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('adresse') border-red-500 @enderror"
                               placeholder="Adresse complète">
                        @error('adresse')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Indicateur de force du mot de passe -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Force du mot de passe:</span>
                    <span id="passwordStrength" class="text-xs font-medium">Faible</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="passwordStrengthBar" class="bg-red-500 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 2. INFORMATIONS PROFESSIONNELLES             -->
            <!-- ============================================ -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-briefcase text-purple-600 mr-2"></i>
                    Informations professionnelles
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Matricule -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Matricule <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="matricule" 
                               value="{{ old('matricule') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('matricule') border-red-500 @enderror"
                               placeholder="ENS001"
                               required>
                        @error('matricule')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Spécialité -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Spécialité <span class="text-red-500">*</span>
                        </label>
                        <select name="specialite" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('specialite') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionner une spécialité</option>
                            @foreach($specialites as $specialite)
                                <option value="{{ $specialite }}" {{ old('specialite') == $specialite ? 'selected' : '' }}>
                                    {{ $specialite }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialite')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date d'embauche -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Date d'embauche <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="date_embauche" 
                               value="{{ old('date_embauche') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('date_embauche') border-red-500 @enderror"
                               required>
                        @error('date_embauche')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 3. AFFECTATIONS                              -->
            <!-- ============================================ -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-tasks text-purple-600 mr-2"></i>
                    Affectations
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Classes (professeur principal) -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Professeur principal de
                        </label>
                        <select name="classes[]" 
                                multiple 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('classes') border-red-500 @enderror"
                                size="4">
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ collect(old('classes'))->contains($classe->id) ? 'selected' : '' }}>
                                    {{ $classe->nom }} ({{ $classe->niveau }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400">Classes dont il sera professeur principal</p>
                        @error('classes')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Classes enseignées -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Classes enseignées
                        </label>
                        <select name="classes_enseignees[]" 
                                multiple 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('classes_enseignees') border-red-500 @enderror"
                                size="4">
                            @foreach($classes as $classe)
                                <option value="{{ $classe->id }}" {{ collect(old('classes_enseignees'))->contains($classe->id) ? 'selected' : '' }}>
                                    {{ $classe->nom }} ({{ $classe->niveau }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400">Classes où il enseignera</p>
                        @error('classes_enseignees')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Matières enseignées -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Matières enseignées
                        </label>
                        <select name="matieres[]" 
                                multiple 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('matieres') border-red-500 @enderror"
                                size="4">
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}" {{ collect(old('matieres'))->contains($matiere->id) ? 'selected' : '' }}>
                                    {{ $matiere->nom }} (Coeff. {{ $matiere->coefficient }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400">Matières qu'il enseignera</p>
                        @error('matieres')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Message de sécurité en bas -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <span class="font-medium">Sécurité :</span>
                            L'enseignant sera automatiquement rattaché à votre établissement 
                            <strong>{{ Auth::user()->etablissement->nom }}</strong>. 
                            Vous ne pouvez affecter que des classes de cet établissement.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 4. BOUTONS D'ACTION                          -->
            <!-- ============================================ -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <p class="text-sm text-gray-500 mr-auto">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    Tous les champs marqués <span class="text-red-500">*</span> sont obligatoires
                </p>
                <a href="{{ route('etablissement.enseignants.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fonction pour afficher/masquer le mot de passe
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Validation du format téléphone (optionnel)
    document.getElementById('telephone')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 8) {
                // Format local: 771234567
                e.target.value = value;
            } else {
                // Format international: 221771234567
                e.target.value = '+' + value;
            }
        }
    });

    // Indicateur de force du mot de passe
    document.getElementById('password')?.addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrength');
        
        let strength = 0;
        let color = 'bg-red-500';
        let text = 'Faible';
        
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]+/)) strength += 25;
        if (password.match(/[A-Z]+/)) strength += 25;
        if (password.match(/[0-9]+/)) strength += 25;
        if (password.match(/[$@#&!]+/)) strength += 25;
        
        strength = Math.min(strength, 100);
        
        if (strength >= 75) {
            color = 'bg-green-500';
            text = 'Fort';
        } else if (strength >= 50) {
            color = 'bg-yellow-500';
            text = 'Moyen';
        } else if (strength >= 25) {
            color = 'bg-orange-500';
            text = 'Faible';
        }
        
        strengthBar.style.width = strength + '%';
        strengthBar.className = `h-2 rounded-full ${color}`;
        strengthText.textContent = text;
        strengthText.className = `text-xs font-medium ${
            strength >= 75 ? 'text-green-600' : 
            strength >= 50 ? 'text-yellow-600' : 
            'text-red-600'
        }`;
    });

    // Aide à la sélection multiple
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('select[multiple]');
        selects.forEach(select => {
            const helpText = document.createElement('p');
            helpText.className = 'text-xs text-gray-400 mt-1';
            helpText.innerHTML = '<i class="fas fa-info-circle mr-1"></i>Maintenez Ctrl pour sélectionner plusieurs options';
            select.parentNode.appendChild(helpText);
        });
    });
</script>
@endpush

@push('styles')
<style>
    select[multiple] {
        min-height: 120px;
    }
    
    select[multiple] option:checked {
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        color: white;
    }
    
    select[multiple] option:hover {
        background-color: #f3e8ff;
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .7; }
    }
</style>
@endpush