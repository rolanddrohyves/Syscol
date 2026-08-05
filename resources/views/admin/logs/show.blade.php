{{-- resources/views/admin/logs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détail du journal - Administration')
@section('page-title', 'Détail du journal d\'activité')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-history mr-2"></i>
                    Détail du journal
                </h3>
                <a href="{{ route('admin.logs.index') }}" 
                   class="text-white hover:text-indigo-200 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>
        
        <div class="p-6">
            @if($log)
            <!-- Informations générales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">ID</p>
                    <p class="font-medium text-gray-800">#{{ $log->id }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">Date et heure</p>
                    <p class="font-medium text-gray-800">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">Utilisateur</p>
                    <p class="font-medium text-gray-800">{{ $log->user->name ?? 'Système' }}</p>
                    <p class="text-xs text-gray-400">{{ $log->user->email ?? '' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">Action</p>
                    <div class="flex items-center mt-1">
                        <i class="{{ $log->action_icon }} mr-2 text-{{ $log->action_color }}-500"></i>
                        <span class="font-medium text-gray-800">{{ $log->action_label }}</span>
                        <span class="ml-2 text-xs text-gray-400">({{ $log->action }})</span>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Description</h4>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-gray-800">{{ $log->description ?? 'Aucune description' }}</p>
                </div>
            </div>
            
            <!-- Informations sur le modèle concerné -->
            @if($log->model_type)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">Modèle concerné</p>
                    <p class="font-medium text-gray-800">{{ class_basename($log->model_type) }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">ID du modèle</p>
                    <p class="font-medium text-gray-800">#{{ $log->model_id }}</p>
                </div>
            </div>
            @endif
            
            <!-- Anciennes valeurs -->
            @if($log->old_values)
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Anciennes valeurs</h4>
                <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto">
                    <pre class="text-green-400 text-sm font-mono">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
            
            <!-- Nouvelles valeurs -->
            @if($log->new_values)
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Nouvelles valeurs</h4>
                <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto">
                    <pre class="text-blue-400 text-sm font-mono">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif
            
            <!-- IP et User Agent -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">Adresse IP</p>
                    <p class="font-medium text-gray-800">{{ $log->ip_address ?? 'Non disponible' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-sm text-gray-500">User Agent</p>
                    <p class="font-medium text-gray-800 text-sm break-all">{{ $log->user_agent ?? 'Non disponible' }}</p>
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.logs.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
                <form action="{{ route('admin.logs.destroy', $log->id) }}" 
                      method="POST" 
                      class="inline-block"
                      onsubmit="return confirm('Supprimer définitivement ce journal ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </form>
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-history text-5xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">Journal non trouvé</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection