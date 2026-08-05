{{-- resources/views/eleve/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier mon profil - Élève')
@section('page-title', 'Modifier mon profil')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-edit mr-2"></i>Modifier mes informations
            </h3>
        </div>
        
        <form action="{{ route('eleve.profile.update') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $eleve->prenom) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('prenom') border-red-500 @enderror">
                    @error('prenom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom', $eleve->nom) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('nom') border-red-500 @enderror">
                    @error('nom')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="tel" name="telephone" value="{{ old('telephone', $user->telephone) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance', $eleve->date_naissance) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lieu de naissance</label>
                    <input type="text" name="lieu_naissance" value="{{ old('lieu_naissance', $eleve->lieu_naissance) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                    <textarea name="adresse" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('adresse', $user->adresse) }}</textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('eleve.profile.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
    
    <!-- Section changement de mot de passe -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-lock mr-2 text-indigo-600"></i>Changer mon mot de passe
            </h3>
        </div>
        
        <form action="{{ route('eleve.profile.password') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe actuel *</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('current_password') border-red-500 @enderror">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            
            <div class="flex justify-end mt-4">
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-key mr-2"></i>Changer le mot de passe
                </button>
            </div>
        </form>
    </div>
    
    <!-- Section photo de profil -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-camera mr-2 text-indigo-600"></i>Photo de profil
            </h3>
        </div>
        
        <form action="{{ route('eleve.profile.photo') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('POST')
            
            <div class="flex items-center space-x-6">
                <div>
                    @if($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" alt="Photo" class="w-24 h-24 rounded-full object-cover">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user-graduate text-3xl text-gray-400"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" name="photo" accept="image/*" required
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-400 mt-1">Format JPG, PNG, GIF. Max 2MB.</p>
                    @error('photo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-upload mr-2"></i>Uploader
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection