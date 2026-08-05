{{-- resources/views/parent/communications/sent.blade.php --}}
@extends('layouts.app')

@section('title', 'Messages envoyés - Parent')
@section('page-title', 'Messages envoyés')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête avec statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total envoyés</p>
                    <p class="text-2xl font-bold">{{ $stats['total_envoyes'] ?? 0 }}</p>
                </div>
                <i class="fas fa-paper-plane text-2xl text-blue-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Ce mois-ci</p>
                    <p class="text-2xl font-bold">{{ $stats['mois_courant'] ?? 0 }}</p>
                </div>
                <i class="fas fa-calendar-alt text-2xl text-green-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Taux de réponse</p>
                    <p class="text-2xl font-bold">{{ $stats['taux_reponse'] ?? 0 }}%</p>
                </div>
                <i class="fas fa-chart-line text-2xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <i class="fas fa-envelope-open-text text-indigo-600 text-xl"></i>
                <span class="text-gray-700">Tous vos messages envoyés</span>
            </div>
            <a href="{{ route('parent.communications.create') }}" 
               class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>Nouveau message
            </a>
        </div>
    </div>

    <!-- Liste des messages envoyés -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-paper-plane mr-2 text-indigo-600"></i>
                Historique des messages envoyés
            </h3>
        </div>

        @if($messagesEnvoyes && $messagesEnvoyes->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($messagesEnvoyes as $message)
                <div class="p-5 hover:bg-gray-50 transition-colors group">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center flex-wrap gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1 text-xs"></i>Envoyé
                                </span>
                                <span class="text-xs text-gray-400">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $message['date'] ?? 'Date inconnue' }}
                                </span>
                            </div>
                            
                            <div class="mb-2">
                                <span class="text-xs text-gray-500">À:</span>
                                <span class="text-sm font-medium text-gray-700 ml-1">
                                    {{ $message['destinataire'] ?? 'Destinataire inconnu' }}
                                </span>
                            </div>
                            
                            <h4 class="font-semibold text-gray-800 mb-2 text-lg">
                                {{ $message['sujet'] ?? 'Sans sujet' }}
                            </h4>
                            
                            <p class="text-gray-600 line-clamp-3 mb-3">
                                {{ $message['message'] ?? '' }}
                            </p>
                            
                            @if(isset($message['eleve_nom']) && $message['eleve_nom'])
                                <p class="text-xs text-gray-400">
                                    <i class="fas fa-user-graduate mr-1"></i>
                                    Élève concerné: {{ $message['eleve_nom'] }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="{{ route('parent.communications.show', $message['id']) }}" 
                               class="px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-lg text-sm transition-colors">
                                <i class="fas fa-eye mr-1"></i> Voir
                            </a>
                            <form action="{{ route('parent.communications.destroy', $message['id']) }}" 
                                  method="POST" 
                                  class="inline-block"
                                  onsubmit="return confirm('Supprimer définitivement ce message ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-trash mr-1"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-16 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-paper-plane text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun message envoyé</h3>
                <p class="text-gray-500 mb-6">Vous n'avez pas encore envoyé de messages</p>
                <a href="{{ route('parent.communications.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Envoyer mon premier message
                </a>
            </div>
        @endif
    </div>
    
    <!-- Conseils -->
    <div class="mt-6 bg-blue-50 rounded-xl p-4 border border-blue-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-lightbulb text-yellow-500 text-lg"></i>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-gray-800">Conseils pour bien communiquer</h4>
                <ul class="mt-1 text-sm text-gray-600 space-y-1">
                    <li>• Soyez précis dans l'objet de votre message pour un meilleur suivi</li>
                    <li>• Identifiez clairement l'enfant concerné par votre demande</li>
                    <li>• Un délai de réponse de 48h est à prévoir hors week-end et jours fériés</li>
                    <li>• Pour les urgences, privilégiez le contact téléphonique avec l'établissement</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection