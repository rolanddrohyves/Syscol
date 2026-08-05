<?php
// app/Http/Controllers/Eleve/ProfilController.php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Eleve;
use App\Models\User;
use App\Models\Note;
use App\Models\Absence;
use App\Models\Bulletin;
use App\Models\Classe;

class ProfilController extends Controller
{
    /**
     * Récupère ou crée l'élève
     */
    private function getOrCreateEleve($user)
    {
        // Chercher l'élève existant
        $eleve = Eleve::where('user_id', $user->id)->first();
        
        if (!$eleve) {
            $eleve = Eleve::where('email', $user->email)->first();
        }
        
        // Si pas trouvé, créer un nouvel élève
        if (!$eleve) {
            $classe = Classe::first();
            
            if ($classe) {
                $eleve = Eleve::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'email_parent' => $user->email,
                    'nom' => explode(' ', $user->name)[1] ?? $user->name,
                    'prenom' => explode(' ', $user->name)[0] ?? 'Élève',
                    'matricule' => 'MAT_' . $user->id . '_' . time(),
                    'classe_id' => $classe->id,
                    'date_naissance' => '2000-01-01',
                    'lieu_naissance' => 'Inconnu',
                    'sexe' => 'M',
                    'adresse' => 'Non renseignée',
                    'telephone_parent' => $user->telephone ?? 'Non renseigné',
                    'nom_parent' => $user->name,
                    'status' => 'actif'
                ]);
            }
        }
        
        return $eleve;
    }
    
    /**
     * Affiche le profil de l'élève
     */
    public function index()
    {
        $user = Auth::user();
        
        $eleve = $this->getOrCreateEleve($user);
        
        if (!$eleve) {
            return redirect()->route('eleve.dashboard')->with('error', 'Impossible de créer le profil élève.');
        }
        
        $stats = [
            'total_notes' => Note::where('eleve_id', $eleve->id)->count(),
            'moyenne_generale' => round(Note::where('eleve_id', $eleve->id)->avg('note') ?? 0, 2),
            'total_absences' => Absence::where('eleve_id', $eleve->id)->where('type', 'absence')->count(),
            'bulletins' => Bulletin::where('eleve_id', $eleve->id)->count(),
        ];
        
        return view('eleve.profile.index', compact('user', 'eleve', 'stats'));
    }
    
    /**
     * Affiche le formulaire d'édition du profil
     */
    public function edit()
    {
        $user = Auth::user();
        
        $eleve = $this->getOrCreateEleve($user);
        
        if (!$eleve) {
            return redirect()->route('eleve.dashboard')->with('error', 'Profil élève non trouvé.');
        }
        
        return view('eleve.profile.edit', compact('user', 'eleve'));
    }
    
    /**
     * Met à jour le profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $eleve = $this->getOrCreateEleve($user);
        
        if (!$eleve) {
            return redirect()->route('eleve.dashboard')->with('error', 'Profil élève non trouvé.');
        }
        
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:255',
        ]);
        
        $user->update([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
        ]);
        
        $eleve->update([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'date_naissance' => $request->date_naissance,
            'lieu_naissance' => $request->lieu_naissance,
            'adresse' => $request->adresse,
            'telephone_parent' => $request->telephone_parent ?? $eleve->telephone_parent,
        ]);
        
        return redirect()->route('eleve.profile.index')->with('success', 'Profil mis à jour avec succès.');
    }
    
    /**
     * Met à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('eleve.profile.index')->with('success', 'Mot de passe modifié avec succès.');
    }
    
    /**
     * Met à jour la photo de profil
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048|mimes:jpeg,png,jpg,gif',
        ]);
        
        $user = Auth::user();
        
        // Supprimer l'ancienne photo
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        
        // Enregistrer la nouvelle photo
        $file = $request->file('photo');
        $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profiles', $filename, 'public');
        
        $user->photo = $path;
        $user->save();
        
        return redirect()->route('eleve.profile.index')->with('success', 'Photo de profil mise à jour avec succès.');
    }
}