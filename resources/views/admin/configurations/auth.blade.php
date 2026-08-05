{{-- resources/views/admin/configurations/auth.blade.php --}}
@extends('layouts.app')

@section('title', 'Configuration authentification - SYSCOL')
@section('page-title', 'Configuration authentification')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-lock text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Authentification</h2>
                <p class="text-gray-500">Paramètres de sécurité et de connexion</p>
            </div>
        </div>

        <form action="{{ route('admin.configurations.auth') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Sécurité des mots de passe -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-key text-green-600 mr-2"></i>
                    Politique des mots de passe
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Longueur minimale -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Longueur minimale du mot de passe
                        </label>
                        <input type="number" 
                               name="password_min_length" 
                               value="{{ $configs['password_min_length']->value ?? 8 }}"
                               min="6" max="20"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Tentatives de connexion -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Tentatives de connexion autorisées
                        </label>
                        <input type="number" 
                               name="login_attempts" 
                               value="{{ $configs['login_attempts']->value ?? 5 }}"
                               min="1" max="10"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-400">Nombre de tentatives avant verrouillage</p>
                    </div>

                    <!-- Durée de verrouillage -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Durée de verrouillage (minutes)
                        </label>
                        <input type="number" 
                               name="lockout_duration" 
                               value="{{ $configs['lockout_duration']->value ?? 30 }}"
                               min="1" max="60"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <!-- Exigences supplémentaires -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               name="password_require_uppercase" 
                               id="uppercase"
                               value="1"
                               {{ ($configs['password_require_uppercase']->value ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 rounded">
                        <label for="uppercase" class="text-sm text-gray-700">
                            Exiger au moins une lettre majuscule
                        </label>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               name="password_require_numbers" 
                               id="numbers"
                               value="1"
                               {{ ($configs['password_require_numbers']->value ?? true) ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 rounded">
                        <label for="numbers" class="text-sm text-gray-700">
                            Exiger au moins un chiffre
                        </label>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               name="password_require_symbols" 
                               id="symbols"
                               value="1"
                               {{ ($configs['password_require_symbols']->value ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 rounded">
                        <label for="symbols" class="text-sm text-gray-700">
                            Exiger au moins un caractère spécial (@, #, $, etc.)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sessions -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-clock text-green-600 mr-2"></i>
                    Gestion des sessions
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Durée de session -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Durée de session (minutes)
                        </label>
                        <input type="number" 
                               name="session_lifetime" 
                               value="{{ $configs['session_lifetime']->value ?? env('SESSION_LIFETIME', 120) }}"
                               min="60" max="10080"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-400">Durée avant déconnexion automatique (1 min - 7 jours)</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <input type="checkbox" 
                           name="two_factor_auth" 
                           id="2fa"
                           value="1"
                           {{ ($configs['two_factor_auth']->value ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 rounded">
                    <label for="2fa" class="text-sm text-gray-700">
                        Activer l'authentification à deux facteurs (2FA)
                    </label>
                </div>

                <div class="flex items-center space-x-3">
                    <input type="checkbox" 
                           name="email_verification" 
                           id="email_verif"
                           value="1"
                           {{ ($configs['email_verification']->value ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 rounded">
                    <label for="email_verif" class="text-sm text-gray-700">
                        Exiger la vérification par email
                    </label>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.configurations.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection