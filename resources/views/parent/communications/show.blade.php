{{-- resources/views/parent/communications/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détail du message - Parent')
@section('page-title', 'Détail du message')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-envelope mr-2"></i>
                    {{ $message->sujet }}
                </h3>
                <a href="{{ route('parent.communications.index') }}" 
                   class="text-white hover:text-indigo-200 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <!-- En-tête du message -->
            <div class="border-b border-gray-200 pb-4 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">
                            @if($message->sender_id == Auth::id())
                                À
                            @else
                                De
                            @endif
                        </p>
                        <p class="font-medium text-gray-800">
                            @if($message->sender_id == Auth::id())
                                {{ $message->receiver->name ?? 'Administration' }}
                            @else
                                {{ $message->sender->name ?? 'Administration' }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-medium text-gray-800">{{ $message->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    @if($message->eleve)
                    <div>
                        <p class="text-sm text-gray-500">Élève concerné</p>
                        <p class="font-medium text-gray-800">{{ $message->eleve->prenom }} {{ $message->eleve->nom }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Statut</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $message->lu ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            <i class="fas {{ $message->lu ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                            {{ $message->lu ? 'Lu' : 'Non lu' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Contenu du message -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Message</h4>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-gray-800 whitespace-pre-wrap">{{ $message->message }}</p>
                </div>
            </div>
            
            <!-- Fil de discussion (réponses précédentes) -->
            @if($message->replies && $message->replies->count() > 0)
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Conversation</h4>
                @foreach($message->replies as $reply)
                <div class="bg-gray-50 rounded-xl p-4 mb-3">
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-medium text-indigo-600">
                            <i class="fas fa-reply mr-1"></i>
                            {{ $reply->sender->name }}
                            @if($reply->sender_id == Auth::id())
                                <span class="text-xs text-gray-500">(Moi)</span>
                            @endif
                        </span>
                        <span class="text-xs text-gray-400">{{ $reply->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-gray-700">{{ $reply->message }}</p>
                </div>
                @endforeach
            </div>
            @endif
            
            <!-- Formulaire de réponse (uniquement si le message a été reçu par le parent) -->
            @if($message->receiver_id == Auth::id())
            <div class="border-t border-gray-200 pt-4 mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Répondre</h4>
                <form action="{{ route('parent.communications.reply', $message->id) }}" method="POST">
                    @csrf
                    <textarea name="message" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500"
                              placeholder="Écrivez votre réponse..."></textarea>
                    <div class="flex justify-end mt-3">
                        <button type="submit" 
                                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>Envoyer la réponse
                        </button>
                    </div>
                </form>
            </div>
            @endif
            
            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('parent.communications.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
                
                @if($message->sender_id == Auth::id())
                <form action="{{ route('parent.communications.destroy', $message->id) }}" 
                      method="POST" 
                      class="inline-block"
                      onsubmit="return confirm('Supprimer ce message ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection