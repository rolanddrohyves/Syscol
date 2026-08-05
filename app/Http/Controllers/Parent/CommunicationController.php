<?php
// app/Http/Controllers/Parent/CommunicationController.php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CommunicationController extends Controller
{
    /**
     * Affiche la page des communications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer les enfants du parent
        $enfants = Eleve::where('email_parent', $user->email)
            ->orWhere('telephone_parent', $user->telephone)
            ->with(['classe'])
            ->get();
        
        // Récupérer les destinataires possibles
        $destinataires = $this->getDestinatairesPossibles($user->etablissement_id);
        
        // Récupérer les messages envoyés par le parent depuis la base de données
        $messagesEnvoyes = Message::where('sender_id', $user->id)
            ->whereIn('type', ['parent_to_admin'])
            ->with(['receiver', 'eleve'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Récupérer les messages reçus par le parent (réponses des admins)
        $messagesRecus = Message::where('receiver_id', $user->id)
            ->whereIn('type', ['admin_reply', 'admin_to_superadmin'])
            ->with(['sender', 'eleve'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques
        $stats = [
            'total_envoyes' => $messagesEnvoyes->count(),
            'total_recus' => $messagesRecus->count(),
            'non_lus' => $messagesRecus->where('lu', false)->count(),
        ];
        
        return view('parent.communications.index', compact(
            'messagesEnvoyes', 
            'messagesRecus', 
            'stats', 
            'destinataires',
            'enfants'
        ));
    }
    
    /**
     * Affiche le formulaire de création de message
     */
    public function create()
    {
        $user = Auth::user();
        
        // Récupérer les enfants du parent
        $enfants = Eleve::where('email_parent', $user->email)
            ->orWhere('telephone_parent', $user->telephone)
            ->with(['classe'])
            ->get();
        
        // Récupérer les destinataires possibles
        $destinataires = $this->getDestinatairesPossibles($user->etablissement_id);
        
        return view('parent.communications.create', compact('enfants', 'destinataires'));
    }
    
    /**
     * Envoie un nouveau message
     */
    public function send(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'destinataire' => 'required|string',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|min:5',
            'eleve_id' => 'nullable|exists:eleves,id',
            'enseignant_id' => 'nullable|exists:users,id',
            'destinataire_nom' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Déterminer le destinataire (admin établissement par défaut)
        $receiverId = $this->getDestinataireId($request, $user->etablissement_id);
        
        if (!$receiverId) {
            return redirect()->back()
                ->with('error', 'Destinataire non trouvé. Veuillez sélectionner un destinataire valide.')
                ->withInput();
        }
        
        // Créer le message dans la base de données
        try {
            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $receiverId,
                'etablissement_id' => $user->etablissement_id,
                'eleve_id' => $request->eleve_id,
                'sujet' => $request->sujet,
                'message' => $request->message,
                'type' => 'parent_to_admin',
                'status' => 'envoye',
                'lu' => false,
            ]);
            
            Log::info('Message envoyé par parent', [
                'parent_id' => $user->id,
                'message_id' => $message->id,
                'destinataire_id' => $receiverId,
                'sujet' => $request->sujet
            ]);
            
            return redirect()->route('parent.communications.index')
                ->with('success', 'Votre message a été envoyé avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur envoi message parent: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi du message: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Affiche les détails d'un message
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $message = Message::with(['sender', 'receiver', 'eleve', 'replies'])
            ->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->findOrFail($id);
        
        // Marquer comme lu si le parent est le destinataire
        if ($message->receiver_id == $user->id && !$message->lu) {
            $message->update(['lu' => true, 'lu_at' => now()]);
        }
        
        return view('parent.communications.show', compact('message'));
    }
    
    /**
     * Affiche les messages envoyés
     */
    public function sent()
    {
        $user = Auth::user();
        
        $messagesEnvoyes = Message::where('sender_id', $user->id)
            ->whereIn('type', ['parent_to_admin'])
            ->with(['receiver', 'eleve'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('parent.communications.sent', compact('messagesEnvoyes'));
    }
    
    /**
     * Supprime un message
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $message = Message::where('sender_id', $user->id)
            ->findOrFail($id);
        
        $message->delete();
        
        return redirect()->route('parent.communications.index')
            ->with('success', 'Message supprimé avec succès.');
    }
    
    /**
     * Marquer un message comme lu
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        
        $message = Message::where('receiver_id', $user->id)
            ->findOrFail($id);
        
        $message->update(['lu' => true, 'lu_at' => now()]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Message marqué comme lu.');
    }
    
    /**
     * RÉPONDRE À UN MESSAGE REÇU
     */
    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:5',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $originalMessage = Message::findOrFail($id);
        $user = Auth::user();
        
        // Vérifier que le parent est bien le destinataire du message original
        if ($originalMessage->receiver_id != $user->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas répondre à ce message.');
        }
        
        try {
            $reply = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $originalMessage->sender_id,
                'etablissement_id' => $user->etablissement_id,
                'eleve_id' => $originalMessage->eleve_id,
                'sujet' => 'RE: ' . $originalMessage->sujet,
                'message' => $request->message,
                'type' => 'parent_to_admin',
                'status' => 'envoye',
                'parent_message_id' => $originalMessage->id,
                'lu' => false,
            ]);
            
            return redirect()->route('parent.communications.show', $reply->id)
                ->with('success', 'Votre réponse a été envoyée.');
                
        } catch (\Exception $e) {
            Log::error('Erreur réponse parent: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi de la réponse: ' . $e->getMessage());
        }
    }
    
    /**
     * Récupère l'ID du destinataire en fonction du type
     */
    private function getDestinataireId(Request $request, $etablissementId)
    {
        switch ($request->destinataire) {
            case 'administration':
                $admin = User::where('etablissement_id', $etablissementId)
                    ->whereHas('role', function($q) {
                        $q->where('name', 'admin_etablissement');
                    })
                    ->first();
                return $admin ? $admin->id : null;
                
            case 'directeur_etudes':
                $directeur = User::where('etablissement_id', $etablissementId)
                    ->whereHas('role', function($q) {
                        $q->where('name', 'directeur_etudes');
                    })
                    ->first();
                return $directeur ? $directeur->id : null;
                
            case 'cpe':
                $cpe = User::where('etablissement_id', $etablissementId)
                    ->whereHas('role', function($q) {
                        $q->where('name', 'cpe');
                    })
                    ->first();
                return $cpe ? $cpe->id : null;
                
            case 'professeur_principal':
                if ($request->eleve_id) {
                    $eleve = Eleve::with('classe')->find($request->eleve_id);
                    if ($eleve && $eleve->classe && $eleve->classe->professeur_principal_id) {
                        return $eleve->classe->professeur_principal_id;
                    }
                }
                return null;
                
            case 'enseignant':
                return $request->enseignant_id;
                
            case 'autre':
                if ($request->destinataire_nom) {
                    $user = User::where('email', $request->destinataire_nom)
                        ->orWhere('name', 'like', '%' . $request->destinataire_nom . '%')
                        ->first();
                    return $user ? $user->id : null;
                }
                return null;
                
            default:
                return null;
        }
    }
    
    /**
     * Récupère les destinataires possibles
     */
    private function getDestinatairesPossibles($etablissementId)
    {
        $destinataires = [];
        
        // Enseignants
        $destinataires['enseignants'] = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', function($q) {
                $q->where('name', 'enseignant');
            })
            ->get();
        
        // CPE
        $cpe = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', function($q) {
                $q->where('name', 'cpe');
            })
            ->first();
        $destinataires['cpe'] = $cpe;
        
        // Directeur des études
        $directeur = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', function($q) {
                $q->where('name', 'directeur_etudes');
            })
            ->first();
        $destinataires['directeur'] = $directeur;
        
        // Administration
        $admin = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', function($q) {
                $q->where('name', 'admin_etablissement');
            })
            ->first();
        $destinataires['administration'] = $admin;
        
        return $destinataires;
    }
}