@extends('layouts.app')

@section('title', 'Configuration générale - SYSCOL')
@section('page-title', 'Configuration générale')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-globe text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Configuration générale</h2>
                <p class="text-gray-500">Paramètres de base de l'application</p>
            </div>
        </div>

        <form action="{{ route('admin.configurations.general') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- ============================================ -->
            <!-- SECTION LOGO -->
            <!-- ============================================ -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-image text-indigo-600 mr-2"></i>
                    Logo de l'application
                </h3>

                <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                    <!-- Affichage du logo actuel -->
                    <div class="flex-shrink-0">
                        <div class="relative w-32 h-32 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 overflow-hidden group" id="logoPreviewContainer">
                            @php
                                $logoPath = $configs['app_logo']->value ?? null;
                            @endphp
                            
                            @if($logoPath && file_exists(public_path('storage/' . $logoPath)))
                                <img src="{{ Storage::url($logoPath) }}" 
                                     alt="Logo actuel" 
                                     class="w-full h-full object-contain p-2"
                                     id="currentLogo">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-cloud-upload-alt text-4xl mb-2"></i>
                                    <span class="text-xs text-center">Aucun logo</span>
                                </div>
                            @endif
                            
                            <!-- Overlay au survol -->
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-camera text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Upload nouveau logo -->
                    <div class="flex-1 space-y-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Nouveau logo
                            </label>
                            <div class="flex items-center space-x-4">
                                <input type="file" 
                                       name="app_logo" 
                                       id="logoInput"
                                       accept="image/png,image/jpeg,image/gif,image/svg+xml"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>
                            <p class="text-xs text-gray-400">
                                Formats acceptés : PNG, JPG, GIF, SVG (max. 2MB)
                            </p>
                            @error('app_logo')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Options du logo -->
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="remove_logo" 
                                       value="1"
                                       class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-700">Supprimer le logo actuel</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de l'application -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                    Informations de l'application
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom de l'application -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Nom de l'application
                        </label>
                        <input type="text" 
                               name="app_name" 
                               value="{{ $configs['app_name']->value ?? env('APP_NAME', 'SYSCOL') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('app_name') border-red-500 @enderror">
                        <p class="text-xs text-gray-400">Le nom affiché dans l'interface</p>
                        @error('app_name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URL de l'application -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            URL de l'application
                        </label>
                        <input type="url" 
                               name="app_url" 
                               value="{{ $configs['app_url']->value ?? env('APP_URL', 'http://localhost:8000') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('app_url') border-red-500 @enderror">
                        @error('app_url')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mode debug -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Mode debug</label>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="app_debug" value="1" 
                                       {{ (($configs['app_debug']->value ?? env('APP_DEBUG')) == true || ($configs['app_debug']->value ?? env('APP_DEBUG')) == '1' || ($configs['app_debug']->value ?? env('APP_DEBUG')) == 'true') ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">Activé</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="app_debug" value="0" 
                                       {{ (($configs['app_debug']->value ?? env('APP_DEBUG')) == false || ($configs['app_debug']->value ?? env('APP_DEBUG')) == '0' || ($configs['app_debug']->value ?? env('APP_DEBUG')) == 'false') ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600">
                                <span class="ml-2 text-sm text-gray-700">Désactivé</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-400">Active l'affichage des erreurs détaillées</p>
                        @error('app_debug')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                    Localisation
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Langue par défaut -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Langue par défaut
                        </label>
                        <select name="app_locale" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('app_locale') border-red-500 @enderror">
                            @foreach($locales as $code => $name)
                                <option value="{{ $code }}" {{ (($configs['app_locale']->value ?? env('APP_LOCALE', 'fr')) == $code) ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('app_locale')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fuseau horaire -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Fuseau horaire
                        </label>
                        <select name="app_timezone" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('app_timezone') border-red-500 @enderror">
                            @foreach($timezones as $timezone)
                                <option value="{{ $timezone }}" {{ (($configs['app_timezone']->value ?? env('APP_TIMEZONE', 'UTC')) == $timezone) ? 'selected' : '' }}>
                                    {{ $timezone }}
                                </option>
                            @endforeach
                        </select>
                        @error('app_timezone')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Maintenance -->
            <div class="space-y-6 pt-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-tools text-indigo-600 mr-2"></i>
                    Maintenance
                </h3>

                <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="maintenance_mode" value="1" 
                               {{ ($configs['maintenance_mode']->value ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Mode maintenance activé</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="maintenance_mode" value="0" 
                               {{ !($configs['maintenance_mode']->value ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Mode maintenance désactivé</span>
                    </label>
                </div>
                <p class="text-xs text-gray-400">En mode maintenance, seuls les administrateurs peuvent accéder au site</p>
                @error('maintenance_mode')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.configurations.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-xl hover:from-indigo-700 hover:to-blue-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>

        <!-- Messages de session -->
        @if(session('success'))
            <div class="mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif
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
                const previewContainer = document.getElementById('logoPreviewContainer');
                const currentLogo = document.getElementById('currentLogo');
                
                if (currentLogo) {
                    currentLogo.src = e.target.result;
                } else {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Nouveau logo" class="w-full h-full object-contain p-2">`;
                }
            }
            reader.readAsDataURL(file);
        }
    });

    // Confirmation pour la suppression du logo
    document.querySelector('input[name="remove_logo"]').addEventListener('change', function(e) {
        if (this.checked) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer le logo ?')) {
                this.checked = false;
            }
        }
    });
</script>
@endpush