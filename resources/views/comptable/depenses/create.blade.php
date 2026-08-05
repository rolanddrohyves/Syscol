{{-- resources/views/comptable/depenses/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle dépense - SYSCOL')
@section('page-title', 'Ajouter une dépense')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-shopping-cart text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Nouvelle dépense</h2>
                <p class="text-gray-500">Enregistrer une dépense de l'établissement</p>
            </div>
        </div>

        <form action="{{ route('comptable.depenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Libellé et montant -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Libellé <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('libelle') border-red-500 @enderror"
                           placeholder="Ex: Achat fournitures bureau" required>
                    @error('libelle')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Montant (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="montant" value="{{ old('montant') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('montant') border-red-500 @enderror"
                           placeholder="50000" required>
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
                            <option value="{{ $value }}" {{ old('categorie') == $value ? 'selected' : '' }}>
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
                    <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" 
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
                            <option value="{{ $value }}" {{ old('mode_paiement') == $value ? 'selected' : '' }}>
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
                    <input type="text" name="beneficiaire" value="{{ old('beneficiaire') }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('beneficiaire') border-red-500 @enderror"
                           placeholder="Nom du fournisseur">
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
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 @error('description') border-red-500 @enderror"
                          placeholder="Détails de la dépense...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pièce jointe -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                    Pièce jointe (facture, reçu...)
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
                    Enregistrer la dépense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection