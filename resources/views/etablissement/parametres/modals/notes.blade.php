{{-- resources/views/etablissement/parametres/modals/notes.blade.php --}}
@php $configNotes = $etablissement->config_notes ?? []; @endphp

<div id="modal-notes" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Configuration des notes</h3>
            <button onclick="closeModal('notes')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('etablissement.parametres.update-notes') }}" method="POST">
            @csrf
            
            <div class="space-y-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note minimale *</label>
                    <input type="number" name="note_minimale" value="{{ $configNotes['note_minimale'] ?? 0 }}" 
                           min="0" max="20" step="0.5"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note maximale *</label>
                    <input type="number" name="note_maximale" value="{{ $configNotes['note_maximale'] ?? 20 }}" 
                           min="0" max="20" step="0.5"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note éliminatoire</label>
                    <input type="number" name="note_eliminatoire" value="{{ $configNotes['note_eliminatoire'] ?? '' }}" 
                           min="0" max="20" step="0.5"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Moyenne requise</label>
                    <input type="number" name="moyenne_requise" value="{{ $configNotes['moyenne_requise'] ?? 10 }}" 
                           min="0" max="20" step="0.5"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('notes')" 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>