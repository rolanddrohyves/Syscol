{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- En-tête avec photo -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8">
            <div class="flex items-center space-x-6">
                <div class="relative">
                    @if($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" 
                             alt="Photo de profil"
                             class="w-24 h-24 rounded-full border-4 border-white object-cover">
                    @else
                        <div class="w-24 h-24 rounded-full border-4 border-white bg-indigo-800 flex items-center justify-center">
                            <span class="text-3xl font-bold text-white">
                                {{ substr($user->prenom ?? $user->nom, 0, 1) }}{{ substr($user->nom ?? '', 0, 1) }}
                            </span>
                        </div>
                    @endif
                    
                    <button onclick="document.getElementById('photoInput').click()" 
                            class="absolute bottom-0 right-0 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors">
                        <i class="fas fa-camera text-indigo-600"></i>
                    </button>
                    
                    <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        <input type="file" id="photoInput" name="photo" class="hidden" accept="image/*" onchange="document.getElementById('photoForm').submit()">
                    </form>
                </div>
                
                <div class="text-white">
                    <h2 class="text-2xl font-bold">{{ $user->prenom }} {{ $user->nom }}</h2>
                    <p class="text-indigo-100">{{ $user->role->name ?? 'Utilisateur' }}</p>
                    <p class="text-indigo-100 text-sm mt-1">
                        <i class="fas fa-envelope mr-1"></i> {{ $user->email }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Messages de succès -->
        @if(session('success'))
        <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                <span class="text-sm text-green-800">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Informations du profil -->
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nom</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->nom }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Prénom</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->prenom }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <p class="text-gray-900">{{ $user->email }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Téléphone</label>
                    <p class="text-gray-900">{{ $user->telephone ?? 'Non renseigné' }}</p>
                </div>
                
                @if($user->etablissement)
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Établissement</label>
                    <p class="text-gray-900">{{ $user->etablissement->nom }}</p>
                </div>
                @endif
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Membre depuis</label>
                    <p class="text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Dernière connexion</label>
                    <p class="text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Première connexion' }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('profile.edit') }}" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 text-center">
                    <i class="fas fa-edit mr-2"></i>Modifier le profil
                </a>
                
                <button type="button" onclick="showPasswordModal()" class="flex-1 px-4 py-2 border border-indigo-600 text-indigo-600 rounded-xl hover:bg-indigo-50">
                    <i class="fas fa-key mr-2"></i>Changer le mot de passe
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour changer le mot de passe -->
<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Changer le mot de passe</h3>
            <button onclick="hidePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                    <input type="password" name="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:border-transparent" required>
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:border-transparent" required>
                    @error('new_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                    <input type="password" name="new_password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:border-transparent" required>
                </div>
                
                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="hidePasswordModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-xl hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700">
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
    document.getElementById('passwordModal').classList.add('flex');
}

function hidePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
    document.getElementById('passwordModal').classList.remove('flex');
}
</script>
@endsection