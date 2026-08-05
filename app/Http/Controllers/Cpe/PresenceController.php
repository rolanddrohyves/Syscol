<?php
// app/Http/Controllers/Cpe/PresenceController.php

namespace App\Http\Controllers\Cpe;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PresenceController extends Controller
{
    /**
     * Affiche le tableau des présences
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $classeId = $request->get('classe_id');
        
        // ✅ TOUTES les classes pour le filtre (toujours toutes)
        $toutesLesClasses = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom')
            ->get();
        
        // ✅ Classes à afficher (filtrées ou toutes)
        $classesQuery = Classe::where('etablissement_id', $etablissementId)
            ->orderBy('niveau')
            ->orderBy('nom');
        
        if ($classeId) {
            $classesAAfficher = $classesQuery->where('id', $classeId)->get();
        } else {
            $classesAAfficher = $classesQuery->get();
        }
        
        $presencesParClasse = [];
        
        foreach ($classesAAfficher as $classe) {
            // Récupérer les élèves de cette classe
            $eleves = Eleve::where('classe_id', $classe->id)
                ->where('status', 'actif')
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get();
            
            if ($eleves->isEmpty()) {
                continue; // Ignorer les classes sans élèves
            }
            
            $presences = [];
            $present = 0;
            $absent = 0;
            
            foreach ($eleves as $eleve) {
                // Vérifier s'il y a une absence pour cette date
                $absence = Absence::where('eleve_id', $eleve->id)
                    ->whereDate('date', $date)
                    ->first();
                
                $estPresent = !$absence;
                
                if ($estPresent) {
                    $present++;
                } else {
                    $absent++;
                }
                
                $presences[] = [
                    'eleve' => $eleve,
                    'absence' => $absence,
                    'present' => $estPresent,
                    'type' => $absence ? $absence->type : null,
                    'justifiee' => $absence ? $absence->justifiee : false,
                ];
            }
            
            $presencesParClasse[$classe->id] = [
                'classe' => $classe,
                'presences' => $presences,
                'total' => count($presences),
                'present' => $present,
                'absent' => $absent,
            ];
        }
        
        return view('cpe.presences.index', compact(
            'presencesParClasse', 
            'date', 
            'toutesLesClasses', // Pour le filtre
            'classeId'          // Pour garder la sélection
        ));
    }

    /**
     * Marque une présence
     */
    public function marquerPresence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier si une absence existe pour cette date
        $absence = Absence::where('eleve_id', $request->eleve_id)
            ->whereDate('date', $request->date)
            ->first();

        if ($absence) {
            $absence->delete();
        }

        return redirect()->back()->with('success', 'Présence marquée avec succès.');
    }

    /**
     * Marque une absence rapide
     */
    public function marquerAbsence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eleve_id' => 'required|exists:eleves,id',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ✅ Récupérer l'élève pour obtenir sa classe
        $eleve = Eleve::find($request->eleve_id);
        
        // Vérifier si une absence existe déjà
        $absence = Absence::where('eleve_id', $request->eleve_id)
            ->whereDate('date', $request->date)
            ->first();

        if (!$absence) {
            // ✅ AJOUT DU CLASSE_ID OBLIGATOIRE
            Absence::create([
                'eleve_id' => $request->eleve_id,
                'classe_id' => $eleve->classe_id, // ← Ajouté pour éviter l'erreur
                'date' => $request->date,
                'type' => 'absence',
                'justifiee' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Absence marquée avec succès.');
    }

    /**
     * Justifie une absence
     */
    public function justifierAbsence(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'justification' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $absence->update([
            'justifiee' => true,
            'motif' => $request->justification,
        ]);

        return redirect()->back()->with('success', 'Absence justifiée avec succès.');
    }

    /**
     * Export des présences au format CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $etablissementId = $user->etablissement_id;
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $classeId = $request->get('classe_id');
        
        $classesQuery = Classe::where('etablissement_id', $etablissementId)
            ->with(['eleves' => function($q) {
                $q->where('status', 'actif')->orderBy('nom')->orderBy('prenom');
            }])
            ->orderBy('niveau')
            ->orderBy('nom');
        
        if ($classeId) {
            $classes = $classesQuery->where('id', $classeId)->get();
        } else {
            $classes = $classesQuery->get();
        }
        
        $filename = 'presences_' . $date . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // BOM UTF-8 pour Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($handle, ['Classe', 'Élève', 'Prénom', 'Nom', 'Statut', 'Type', 'Justifié']);
        
        foreach ($classes as $classe) {
            foreach ($classe->eleves as $eleve) {
                $absence = Absence::where('eleve_id', $eleve->id)
                    ->whereDate('date', $date)
                    ->first();
                
                fputcsv($handle, [
                    $classe->nom,
                    $eleve->nom . ' ' . $eleve->prenom,
                    $eleve->prenom,
                    $eleve->nom,
                    $absence ? 'Absent' : 'Présent',
                    $absence ? $absence->type : '',
                    $absence && $absence->justifiee ? 'Oui' : 'Non',
                ]);
            }
        }
        
        fclose($handle);
        exit;
    }
}