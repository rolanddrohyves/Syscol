{{-- resources/views/comptable/depenses/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier une dépense - SYSCOL')
@section('page-title', 'Modifier la dépense')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-shopping-cart text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier la dépense</h2>
                <p class="text-gray-500">{{ $depense->libelle }}</p>
            </div>
        </div>

        <form action="{{ route('comptable.depenses.update', $depense->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Libellé et montant -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Libellé <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="libelle" value="{{ old('libelle', $depense->libelle) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('libelle') border-red-500 @enderror"
                           required>
                    @error('libelle')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Montant (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="montant" value="{{ old('montant', $depense->montant) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('montant') border-red-500 @enderror"
                           required>
                    @error('montant')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Catégorie et date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Catégorie <span class="text-red-500">*</span>
                    </label>
                    <select name="categorie" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('categorie') border-red-500 @enderror" required>
                        <option value="">Sélectionner</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('categorie', $depense->categorie) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" value="{{ old('date', $depense->date->format('Y-m-d')) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('date') border-red-500 @enderror"
                           required>
                    @error('date')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Mode paiement et bénéficiaire -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Mode de paiement <span class="text-red-500">*</span>
                    </label>
                    <select name="mode_paiement" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('mode_paiement') border-red-500 @enderror" required>
                        <option value="">Sélectionner</option>
                        @foreach($modes as $value => $label)
                            <option value="{{ $value }}" {{ old('mode_paiement', $depense->mode_paiement) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('mode_paiement')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Bénéficiaire
                    </label>
                    <input type="text" name="beneficiaire" value="{{ old('beneficiaire', $depense->beneficiaire) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('beneficiaire') border-red-500 @enderror">
                    @error('beneficiaire')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Description
                </label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('description') border-red-500 @enderror">{{ old('description', $depense->description) }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pièce jointe actuelle -->
            @if($depense->piece_jointe)
            <div class="p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-paperclip text-blue-600 mr-2"></i>
                        <span class="text-sm text-gray-700">Pièce jointe actuelle</span>
                    </div>
                    <a href="{{ Storage::url($depense->piece_jointe) }}" target="_blank" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-eye mr-1"></i> Voir
                    </a>
                </div>
            </div>
            @endif

            <!-- Nouvelle pièce jointe -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    @if($depense->piece_jointe) Remplacer la pièce jointe @else Pièce jointe @endif
                </label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Cliquez pour télécharger</p>
                            <p class="text-xs text-gray-400">PDF, JPG, PNG (max 2Mo)</p>
                        </div>
                        <input type="file" name="piece_jointe" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                    </label>
                </div>
                @error('piece_jointe')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('comptable.depenses.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-xl hover:from-red-700 hover:to-orange-700 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection