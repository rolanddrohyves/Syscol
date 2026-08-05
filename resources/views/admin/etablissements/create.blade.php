{{-- resources/views/admin/etablissements/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer un établissement - SYSCOL')
@section('page-title', 'Créer un établissement')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-school text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvel établissement</h2>
                <p class="text-gray-500">Ajoutez un nouvel établissement scolaire</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('admin.etablissements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Informations générales -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                    Informations générales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Nom de l'établissement <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nom" 
                               value="{{ old('nom') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('nom') border-red-500 @enderror"
                               placeholder="Ex: Lycée Technique National">
                        @error('nom')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('type') border-red-500 @enderror">
                            <option value="">Sélectionnez un type</option>
                            <option value="Primaire" {{ old('type') == 'Primaire' ? 'selected' : '' }}>Primaire</option>
                            <option value="Collège" {{ old('type') == 'Collège' ? 'selected' : '' }}>Collège</option>
                            <option value="Lycée" {{ old('type') == 'Lycée' ? 'selected' : '' }}>Lycée</option>
                            <option value="Primaire/Secondaire" {{ old('type') == 'Primaire/Secondaire' ? 'selected' : '' }}>Primaire/Secondaire</option>
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Code établissement -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Code établissement <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="code_etablissement" 
                               value="{{ old('code_etablissement') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('code_etablissement') border-red-500 @enderror"
                               placeholder="Ex: LTN001">
                        @error('code_etablissement')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Logo</label>
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300" id="logoPreview">
                                <i class="fas fa-image text-2xl text-gray-400"></i>
                            </div>
                            <div class="flex-1">
                                <input type="file" 
                                       name="logo" 
                                       id="logoInput"
                                       accept="image/*"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG ou GIF (max. 2MB)</p>
                            </div>
                        </div>
                        @error('logo')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Adresse et contact -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                    Adresse et contact
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Adresse -->
                    <div class="col-span-2 space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Adresse <span class="text-red-500">*</span>
                        </label>
                        <textarea name="adresse" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('adresse') border-red-500 @enderror"
                                  placeholder="Adresse complète">{{ old('adresse') }}</textarea>
                        @error('adresse')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Téléphone -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Téléphone <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="telephone" 
                               value="{{ old('telephone') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('telephone') border-red-500 @enderror"
                               placeholder="+221 33 123 45 67">
                        @error('telephone')
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
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                               placeholder="contact@etablissement.sn">
                        @error('email')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Académie et inspectorat -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-university text-indigo-600 mr-2"></i>
                    Académie et inspectorat
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Académie -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Académie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="academie" 
                               value="{{ old('academie') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('academie') border-red-500 @enderror"
                               placeholder="Ex: Dakar">
                        @error('academie')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Inspectorat -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Inspectorat</label>
                        <input type="text" 
                               name="inspectorat" 
                               value="{{ old('inspectorat') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                               placeholder="Ex: IA Dakar">
                        @error('inspectorat')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <div class="flex items-center space-x-3">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        Établissement actif
                    </label>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.etablissements') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Créer l'établissement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Prévisualisation du logo
    document.getElementById('logoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">`;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush