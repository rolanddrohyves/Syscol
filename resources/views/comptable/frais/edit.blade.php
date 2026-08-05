{{-- resources/views/comptable/frais/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier un frais - SYSCOL')
@section('page-title', 'Modifier le frais de scolarité')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <!-- En-tête -->
        <div class="flex items-center space-x-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-tag text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier le frais</h2>
                <p class="text-gray-500">{{ $frais->libelle }}</p>
            </div>
        </div>

        <form action="{{ route('comptable.frais.update', $frais->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Année scolaire -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Année scolaire <span class="text-red-500">*</span>
                    </label>
                    <select name="annee_scolaire_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                        @foreach($anneesScolaires as $annee)
                            <option value="{{ $annee->id }}" {{ $frais->annee_scolaire_id == $annee->id ? 'selected' : '' }}>
                                {{ $annee->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('annee_scolaire_id')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Libellé -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Libellé <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="libelle" value="{{ old('libelle', $frais->libelle) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                    @error('libelle')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ $frais->type == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Périodicité -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Périodicité <span class="text-red-500">*</span>
                    </label>
                    <select name="periodicite" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                        @foreach($periodicites as $value => $label)
                            <option value="{{ $value }}" {{ $frais->periodicite == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('periodicite')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Montant -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Montant (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="montant" value="{{ old('montant', $frais->montant) }}" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500" required>
                    @error('montant')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Obligatoire -->
                <div class="space-y-2 flex items-center">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="obligatoire" value="1" 
                               {{ old('obligatoire', $frais->obligatoire) ? 'checked' : '' }} 
                               class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Frais obligatoire</span>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('description', $frais->description) }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('comptable.frais.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection