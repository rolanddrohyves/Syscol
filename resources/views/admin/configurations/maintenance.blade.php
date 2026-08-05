{{-- resources/views/admin/configurations/maintenance.blade.php --}}
@extends('layouts.app')

@section('title', 'Maintenance - SYSCOL')
@section('page-title', 'Mode maintenance')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-tools text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Mode maintenance</h2>
                <p class="text-gray-500">Mettre le site en maintenance</p>
            </div>
        </div>

        @if($isDown)
            <!-- Mode maintenance actif -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800">Mode maintenance ACTIF</h3>
                        <p class="text-sm text-yellow-700">Le site est actuellement inaccessible au public</p>
                    </div>
                </div>

                <form action="{{ route('admin.configurations.maintenance.disable') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all">
                        <i class="fas fa-play mr-2"></i>
                        Désactiver le mode maintenance
                    </button>
                </form>
            </div>
        @else
            <!-- Mode maintenance inactif -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-800">Mode maintenance inactif</h3>
                        <p class="text-sm text-green-700">Le site est accessible normalement</p>
                    </div>
                </div>
            </div>

            <!-- Formulaire d'activation -->
            <form action="{{ route('admin.configurations.maintenance.enable') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Secret de contournement
                        </label>
                        <input type="text" 
                               name="secret" 
                               value="syscol-maintenance-{{ Str::random(4) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <p class="text-xs text-gray-400">URL secrète pour accéder au site : /{{ Str::random(4) }}</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Temps de réessai (secondes)
                        </label>
                        <input type="number" 
                               name="retry" 
                               value="60"
                               min="30" max="300"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.configurations.index') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl hover:from-yellow-700 hover:to-orange-700 transition-all shadow-lg">
                        <i class="fas fa-pause mr-2"></i>
                        Activer le mode maintenance
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection