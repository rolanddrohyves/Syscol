{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier un utilisateur - SYSCOL')
@section('page-title', 'Modifier un utilisateur')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-edit text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier {{ $user->name }}</h2>
                <p class="text-gray-500">Modifiez les informations de l'utilisateur</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Informations personnelles -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-circle text-yellow-600 mr-2"></i>
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
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                               placeholder="Ex: Jean Dupont"
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
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                               placeholder="jean.dupont@exemple.com"
                               required>
                        @error('email')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Téléphone -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="text" 
                               name="telephone" 
                               value="{{ old('telephone', $user->telephone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('telephone') border-red-500 @enderror"
                               placeholder="+225 07 16 286 319">
                        @error('telephone')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nouveau mot de passe (optionnel) -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Nouveau mot de passe
                        </label>
                        <input type="password" 
                               name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                               placeholder="••••••••">
                        <p class="text-xs text-gray-400">Laissez vide pour conserver le mot de passe actuel</p>
                        @error('password')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmation nouveau mot de passe -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Confirmer le nouveau mot de passe
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

            <!-- Rôle et établissement -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-tag text-yellow-600 mr-2"></i>
                    Rôle et affectation
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Rôle -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Rôle <span class="text-red-500">*</span>
                        </label>
                        <select name="role_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('role_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez un rôle</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name ?? $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Établissement -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Établissement</label>
                        <select name="etablissement_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all @error('etablissement_id') border-red-500 @enderror">
                            <option value="">Aucun établissement</option>
                            @foreach($etablissements as $etablissement)
                                <option value="{{ $etablissement->id }}" {{ old('etablissement_id', $user->etablissement_id) == $etablissement->id ? 'selected' : '' }}>
                                    {{ $etablissement->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('etablissement_id')
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
                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        Compte actif
                    </label>
                </div>
                
                @if(!$user->email_verified_at)
                <div class="flex items-center space-x-3">
                    <input type="checkbox" 
                           name="email_verified" 
                           id="email_verified" 
                           value="1"
                           class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                    <label for="email_verified" class="text-sm font-medium text-gray-700">
                        Marquer l'email comme vérifié
                    </label>
                </div>
                @endif
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.users') }}" 
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