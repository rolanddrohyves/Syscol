{{-- resources/views/admin/configurations/backups.blade.php --}}
@extends('layouts.app')

@section('title', 'Sauvegardes - SYSCOL')
@section('page-title', 'Gestion des sauvegardes')

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-archive text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Sauvegardes</h2>
                <p class="text-sm text-gray-500">Gérez les sauvegardes de la base de données</p>
            </div>
        </div>
        
        <a href="{{ route('admin.configurations.backup') }}" 
           onclick="return confirm('Lancer une sauvegarde maintenant ?')"
           class="flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all">
            <i class="fas fa-database mr-2"></i>
            Nouvelle sauvegarde
        </a>
    </div>

    <!-- Liste des sauvegardes -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fichier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taille</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($backups as $backup)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <i class="fas fa-database text-blue-500 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">{{ $backup['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $backup['size'] }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $backup['date'] }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.configurations.backups.download', $backup['name']) }}" 
                               class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                               title="Télécharger">
                                <i class="fas fa-download"></i>
                            </a>
                            <form action="{{ route('admin.configurations.backups.delete', $backup['name']) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Supprimer cette sauvegarde ?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-database text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune sauvegarde</h3>
                            <p class="text-gray-500">Commencez par créer votre première sauvegarde</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection