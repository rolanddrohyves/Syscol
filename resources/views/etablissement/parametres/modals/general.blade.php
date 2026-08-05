{{-- resources/views/etablissement/parametres/modals/general.blade.php --}}
<div id="modal-general" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Modifier les informations générales</h3>
            <button onclick="closeModal('general')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('etablissement.parametres.update-general') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'établissement *</label>
                    <input type="text" name="nom" value="{{ $etablissement->nom }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="Primaire" {{ $etablissement->type == 'Primaire' ? 'selected' : '' }}>Primaire</option>
                        <option value="Collège" {{ $etablissement->type == 'Collège' ? 'selected' : '' }}>Collège</option>
                        <option value="Lycée" {{ $etablissement->type == 'Lycée' ? 'selected' : '' }}>Lycée</option>
                        <option value="Primaire et Collège" {{ $etablissement->type == 'Primaire et Collège' ? 'selected' : '' }}>Primaire et Collège</option>
                        <option value="Collège et Lycée" {{ $etablissement->type == 'Collège et Lycée' ? 'selected' : '' }}>Collège et Lycée</option>
                        <option value="Primaire et Lycée" {{ $etablissement->type == 'Primaire et Lycée' ? 'selected' : '' }}>Primaire et Lycée</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                    <input type="text" name="adresse" value="{{ $etablissement->adresse }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input type="tel" name="telephone" value="{{ $etablissement->telephone }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ $etablissement->email }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville *</label>
                    <input type="text" name="ville" value="{{ $etablissement->ville }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                    <input type="text" name="code_postal" value="{{ $etablissement->code_postal }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Région</label>
                    <input type="text" name="region" value="{{ $etablissement->region }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('general')" 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>