{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer un utilisateur - SYSCOL')
@section('page-title', 'Créer un utilisateur')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvel utilisateur</h2>
                <p class="text-gray-500">Ajoutez un nouvel utilisateur au système</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Informations personnelles -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-circle text-green-600 mr-2"></i>
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
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
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
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
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
                               value="{{ old('telephone') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('telephone') border-red-500 @enderror"
                               placeholder="+225 07 16 286 319">
                        @error('telephone')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mot de passe -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                               placeholder="••••••••"
                               required>
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
                        <input type="password" 
                               name="password_confirmation" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"
                               placeholder="••••••••"
                               required>
                    </div>
                </div>
            </div>

            <!-- Rôle et établissement -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-user-tag text-green-600 mr-2"></i>
                    Rôle et affectation
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Rôle -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Rôle <span class="text-red-500">*</span>
                        </label>
                        <select name="role_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('role_id') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez un rôle</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all @error('etablissement_id') border-red-500 @enderror">
                            <option value="">Aucun établissement</option>
                            @foreach($etablissements as $etablissement)
                                <option value="{{ $etablissement->id }}" {{ old('etablissement_id') == $etablissement->id ? 'selected' : '' }}>
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
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        Compte actif (l'utilisateur pourra se connecter immédiatement)
                    </label>
                </div>
                
                <div class="flex items-center space-x-3">
                    <input type="checkbox" 
                           name="email_verified" 
                           id="email_verified" 
                           value="1"
                           {{ old('email_verified', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <label for="email_verified" class="text-sm font-medium text-gray-700">
                        Email vérifié (marquer l'email comme vérifié)
                    </label>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.users') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-save mr-2"></i>
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection