{{-- resources/views/admin/roles/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier un rôle - SYSCOL')
@section('page-title', 'Modifier un rôle')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-tag text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier {{ $role->display_name }}</h2>
                <p class="text-gray-500">Modifiez les informations du rôle</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Nom technique -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Nom technique <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $role->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                           placeholder="ex: chef_departement"
                           required>
                    <p class="text-xs text-gray-400">Lettres minuscules et underscores uniquement (ex: chef_departement)</p>
                    @error('name')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom affiché -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Nom affiché <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="display_name" 
                           value="{{ old('display_name', $role->display_name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('display_name') border-red-500 @enderror"
                           placeholder="ex: Chef de département"
                           required>
                    @error('display_name')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('description') border-red-500 @enderror"
                              placeholder="Description des responsabilités liées à ce rôle...">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Niveau hiérarchique -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Niveau hiérarchique <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="range" 
                               name="level" 
                               id="levelRange"
                               min="1" 
                               max="100" 
                               value="{{ old('level', $role->level) }}"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-yellow-600">
                        <span id="levelValue" class="text-lg font-semibold text-yellow-600 min-w-[3rem] text-center">{{ $role->level }}</span>
                    </div>
                    <p class="text-xs text-gray-400">Plus le niveau est élevé, plus le rôle a de privilèges</p>
                    @error('level')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.roles') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mise à jour de l'affichage du niveau
    const levelRange = document.getElementById('levelRange');
    const levelValue = document.getElementById('levelValue');
    
    levelRange.addEventListener('input', function() {
        levelValue.textContent = this.value;
    });
</script>
@endpush