{{-- resources/views/admin/communications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Messagerie - Super Admin')
@section('page-title', 'Messagerie')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-envelope mr-2"></i>
                Messagerie Super Admin
            </h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-sm text-blue-600">Messages non lus</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $stats['non_lus'] ?? 0 }}</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4">
                    <p class="text-sm text-green-600">Messages reçus</p>
                    <p class="text-2xl font-bold text-green-700">{{ $stats['total_recus'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-4">
                    <p class="text-sm text-purple-600">Messages envoyés</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $stats['total_envoyes'] ?? 0 }}</p>
                </div>
            </div>
            
            <h4 class="font-semibold text-gray-800 mb-3">Messages reçus des Administrateurs</h4>
            <div class="divide-y divide-gray-200">
                @forelse($messagesRecus ?? [] as $message)
                <div class="py-3">
                    <a href="{{ route('admin.communications.show', $message->id) }}" class="block hover:bg-gray-50 p-2 rounded-lg">
                        <p class="font-medium">{{ $message->sujet }}</p>
                        <p class="text-sm text-gray-500">De: {{ $message->sender->name ?? 'Admin' }} - {{ $message->created_at->format('d/m/Y H:i') }}</p>
                    </a>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucun message reçu</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection