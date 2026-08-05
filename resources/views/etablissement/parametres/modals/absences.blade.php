{{-- resources/views/etablissement/parametres/modals/absences.blade.php --}}
@php $configAbsences = $etablissement->config_absences ?? []; @endphp

<div id="modal-absences" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Configuration des absences</h3>
            <button onclick="closeModal('absences')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('etablissement.parametres.update-absences') }}" method="POST">
            @csrf
            
            <div class="space-y-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seuil d'alerte *</label>
                    <input type="number" name="seuil_alerte_absence" value="{{ $configAbsences['seuil_alerte_absence'] ?? 5 }}" 
                           min="1" max="30"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Délai de justification *</label>
                    <input type="number" name="justification_delai" value="{{ $configAbsences['justification_delai'] ?? 7 }}" 
                           min="1" max="15"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                           required>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="notification_parents" id="notification_parents" value="1"
                           {{ ($configAbsences['notification_parents'] ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                    <label for="notification_parents" class="ml-2 text-sm text-gray-700">
                        Notifier les parents en cas d'absence
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('absences')" 
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>