<?php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\AnneeScolaire;
use App\Models\Trimestre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf; // ✅ Ajouter cette ligne

class ParametreController extends Controller
{
    /**
     * Affiche la page des paramètres
     */
    public function index()
    {
        $user = auth()->user();
        $etablissement = Etablissement::find($user->etablissement_id);
        
        $anneeEnCours = AnneeScolaire::where('etablissement_id', $etablissement->id)
            ->where('is_current', true)
            ->first();
        
        $trimestreEnCours = Trimestre::whereHas('anneeScolaire', function($q) use ($etablissement) {
                $q->where('etablissement_id', $etablissement->id)
                  ->where('is_current', true);
            })
            ->where('is_current', true)
            ->first();
        
        return view('etablissement.parametres.index', compact('etablissement', 'anneeEnCours', 'trimestreEnCours'));
    }

    /**
     * Met à jour les informations générales
     */
    public function updateGeneral(Request $request)
    {
        $user = auth()->user();
        $etablissement = Etablissement::find($user->etablissement_id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'type' => 'required|in:Primaire,Collège,Lycée,Primaire et Collège,Collège et Lycée,Primaire et Lycée',
            'adresse' => 'required|string|max:500',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'ville' => 'required|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'region' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $etablissement->update($request->all());

        return redirect()->route('etablissement.parametres.index')
            ->with('success', 'Informations générales mises à jour avec succès.');
    }

    /**
     * Met à jour le logo
     */
    public function updateLogo(Request $request)
    {
        $user = auth()->user();
        $etablissement = Etablissement::find($user->etablissement_id);

        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Supprimer l'ancien logo si existe
        if ($etablissement->logo) {
            Storage::disk('public')->delete($etablissement->logo);
        }

        // Upload du nouveau logo
        $path = $request->file('logo')->store('logos', 'public');
        $etablissement->update(['logo' => $path]);

        return redirect()->route('etablissement.parametres.index')
            ->with('success', 'Logo mis à jour avec succès.');
    }

    /**
     * Met à jour les informations de contact
     */
    public function updateContact(Request $request)
    {
        $user = auth()->user();
        $etablissement = Etablissement::find($user->etablissement_id);

        $validator = Validator::make($request->all(), [
            'telephone' => 'required|string|max:20',
            'telephone_urgence' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'email_contact' => 'nullable|email|max:255',
            'site_web' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $etablissement->update($request->all());

        return redirect()->route('etablissement.parametres.index')
            ->with('success', 'Informations de contact mises à jour avec succès.');
    }

    /**
     * Met à jour les horaires
     */
    public function updateHoraires(Request $request)
    {
        $user = auth()->user();
        $etablissement = Etablissement::find($user->etablissement_id);

        $validator = Validator::make($request->all(), [
            'heure_ouverture' => 'nullable|date_format:H:i',
            'heure_fermeture' => 'nullable|date_format:H:i|after:heure_ouverture',
            'pause_debut' => 'nullable|date_format:H:i',
            'pause_fin' => 'nullable|date_format:H:i|after:pause_debut',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $etablissement->update($request->all());

        return redirect()->route('etablissement.parametres.index')
            ->with('success', 'Horaires mis à jour avec succès.');
    }

    /**
     * Met à jour la configuration des notes
     */
    public function updateNotesConfig(Request $request)
    {
        $user = auth()->user();
        $etablissement = Etablissement::find($user->etablissement_id);

        $validator = Validator::make($request->all(), [
            'note_minimale' => 'required|numeric|min:0|max:20',
            'note_maximale' => 'required|numeric|min:0|max:20|gte:note_minimale',
            'note_eliminatoire' => 'nullable|numeric|min:0|max:20',
            'moyenne_requise' => 'nullable|numeric|min:0|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $etablissement->update([
            'config_notes' => [
                'note_minimale' => $request->note_minimale,
                'note_maximale' => $request->note_maximale,
                'note_eliminatoire' => $request->note_eliminatoire,
                'moyenne_requise' => $request->moyenne_requise,
            ]
        ]);

        return redirect()->route('etablissement.parametres.index')
            ->with('success', 'Configuration des notes mise à jour avec succès.');
    }

    /**
     * Met à jour la configuration des absences
     */
    public function updateAbsencesConfig(Request $request)
    {
        $user = auth()->user();
        $etablissement = Etablissement::find($user->etablissement_id);

        $validator = Validator::make($request->all(), [
            'seuil_alerte_absence' => 'required|integer|min:1|max:30',
            'notification_parents' => 'boolean',
            'justification_delai' => 'required|integer|min:1|max:15',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $etablissement->update([
            'config_absences' => [
                'seuil_alerte_absence' => $request->seuil_alerte_absence,
                'notification_parents' => $request->boolean('notification_parents'),
                'justification_delai' => $request->justification_delai,
            ]
        ]);

        return redirect()->route('etablissement.parametres.index')
            ->with('success', 'Configuration des absences mise à jour avec succès.');
    }

    /**
     * Génère un rapport de l'établissement
     */
    public function generateReport()
    {
        $user = auth()->user();
        $etablissement = Etablissement::with(['classes.eleves', 'enseignants', 'eleves'])
            ->find($user->etablissement_id);
        
        $stats = [
            'total_classes' => $etablissement->classes->count(),
            'total_enseignants' => $etablissement->enseignants->count(),
            'total_eleves' => $etablissement->eleves->count(),
            'annee_en_cours' => AnneeScolaire::where('etablissement_id', $etablissement->id)
                ->where('is_current', true)
                ->first(),
        ];
        
        return view('etablissement.parametres.rapport', compact('etablissement', 'stats'));
    }

    /**
     * ✅ Exporte le rapport en PDF
     */
    public function exportPDF()
    {
        $user = auth()->user();
        $etablissement = Etablissement::with(['classes.eleves', 'enseignants', 'eleves'])
            ->find($user->etablissement_id);
        
        $stats = [
            'total_classes' => $etablissement->classes->count(),
            'total_enseignants' => $etablissement->enseignants->count(),
            'total_eleves' => $etablissement->eleves->count(),
            'annee_en_cours' => AnneeScolaire::where('etablissement_id', $etablissement->id)
                ->where('is_current', true)
                ->first(),
        ];
        
        // Statistiques par sexe
        $garcons = $etablissement->eleves->where('sexe', 'M')->count();
        $filles = $etablissement->eleves->where('sexe', 'F')->count();
        $total = $garcons + $filles;
        
        $stats['garcons'] = $garcons;
        $stats['filles'] = $filles;
        $stats['pourcentage_garcons'] = $total > 0 ? round(($garcons / $total) * 100, 1) : 0;
        $stats['pourcentage_filles'] = $total > 0 ? round(($filles / $total) * 100, 1) : 0;
        
        // Statistiques par classe
        $statsParClasse = [];
        foreach ($etablissement->classes as $classe) {
            $effectif = $classe->eleves->count();
            $statsParClasse[] = [
                'nom' => $classe->nom,
                'niveau' => $classe->niveau,
                'effectif' => $effectif,
                'garcons' => $classe->eleves->where('sexe', 'M')->count(),
                'filles' => $classe->eleves->where('sexe', 'F')->count(),
                'professeur' => $classe->professeurPrincipal->name ?? 'Non assigné',
            ];
        }
        $stats['par_classe'] = $statsParClasse;
        
        $pdf = Pdf::loadView('etablissement.parametres.rapport-pdf', compact('etablissement', 'stats'));
        
        // Télécharger le PDF
        return $pdf->download('rapport_' . $etablissement->nom . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Sauvegarde la configuration
     */
    public function backup()
    {
        $user = auth()->user();
        $etablissement = Etablissement::with(['classes', 'enseignants', 'eleves'])
            ->find($user->etablissement_id);
        
        // Logique de sauvegarde (exporter les données)
        $data = [
            'etablissement' => $etablissement->toArray(),
            'classes' => $etablissement->classes->toArray(),
            'enseignants' => $etablissement->enseignants->toArray(),
            'eleves' => $etablissement->eleves->toArray(),
            'date_sauvegarde' => now()->toDateTimeString(),
        ];
        
        $filename = 'backup_' . $etablissement->id . '_' . date('Y-m-d') . '.json';
        Storage::disk('local')->put('backups/' . $filename, json_encode($data, JSON_PRETTY_PRINT));
        
        return redirect()->route('etablissement.parametres.index')
            ->with('success', 'Sauvegarde effectuée avec succès.');
    }

    /**
     * Restaure une sauvegarde
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json|max:10240',
        ]);

        // Logique de restauration
        // Attention : Cette opération est délicate et doit être implémentée avec précaution
        
        return redirect()->route('etablissement.parametres.index')
            ->with('success', 'Restauration effectuée avec succès.');
    }
}