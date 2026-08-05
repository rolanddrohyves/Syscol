<?php
// app/Http/Controllers/Etablissement/MessageController.php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Eleve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Affiche la messagerie de l'ADMIN ÉTABLISSEMENT
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $etablissementId = $user->etablissement_id;
        
        // Messages reçus des parents
        $messagesRecus = Message::where('receiver_id', $user->id)
            ->where('type', 'parent_to_admin')
            ->with(['sender', 'eleve', 'replies'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Messages envoyés par l'admin
        $messagesEnvoyes = Message::where('sender_id', $user->id)
            ->whereIn('type', ['admin_reply', 'admin_to_superadmin'])
            ->with(['receiver', 'eleve'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques
        $stats = [
            'non_lus' => Message::where('receiver_id', $user->id)
                ->where('type', 'parent_to_admin')
                ->where('lu', false)
                ->count(),
            'total_recus' => Message::where('receiver_id', $user->id)
                ->where('type', 'parent_to_admin')
                ->count(),
            'total_envoyes' => Message::where('sender_id', $user->id)
                ->whereIn('type', ['admin_reply', 'admin_to_superadmin'])
                ->count(),
            'aujourd_hui' => Message::where('receiver_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
        ];
        
        // Récupérer les parents (sans la relation eleves)
        $parents = User::where('etablissement_id', $etablissementId)
            ->whereHas('role', function($q) {
                $q->where('name', 'parent');
            })
            ->get();
        
        // Récupérer manuellement les élèves pour chaque parent
        foreach ($parents as $parent) {
            $parent->enfants = Eleve::where('parent_id', $parent->id)
                ->orWhere('email_parent', $parent->email)
                ->orWhere('telephone_parent', $parent->telephone)
                ->with(['classe'])
                ->get();
        }
        
        // Récupérer les élèves pour le filtrage
        $eleves = Eleve::whereHas('classe', function($q) use ($etablissementId) {
            $q->where('etablissement_id', $etablissementId);
        })->with(['classe'])->get();
        
        // Super admin
        $superAdmin = User::whereHas('role', function($q) {
            $q->where('name', 'super_admin');
        })->first();
        
        return view('etablissement.communications.index', compact(
            'messagesRecus', 
            'messagesEnvoyes', 
            'stats', 
            'parents',
            'eleves',
            'superAdmin'
        ));
    }
    
    /**
     * Envoie un message à un parent
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|min:5',
            'eleve_id' => 'nullable|exists:eleves,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = Auth::user();
        
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'etablissement_id' => $user->etablissement_id,
            'eleve_id' => $request->eleve_id,
            'sujet' => $request->sujet,
            'message' => $request->message,
            'type' => 'admin_reply',
            'status' => 'envoye',
            'lu' => false,
        ]);
        
        return redirect()->route('etablissement.communications.show', $message->id)
            ->with('success', 'Message envoyé avec succès.');
    }
    
    /**
     * Affiche un message spécifique
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $message = Message::with(['sender', 'receiver', 'eleve', 'replies.sender'])
            ->findOrFail($id);
        
        // Vérifier l'accès
        if ($message->receiver_id != $user->id && $message->sender_id != $user->id) {
            abort(403);
        }
        
        // Marquer comme lu
        if ($message->receiver_id == $user->id && !$message->lu) {
            $message->update(['lu' => true, 'lu_at' => now()]);
        }
        
        $superAdmin = User::whereHas('role', function($q) {
            $q->where('name', 'super_admin');
        })->first();
        
        $parents = User::where('etablissement_id', $user->etablissement_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'parent');
            })
            ->get();
        
        return view('etablissement.communications.show', compact('message', 'superAdmin', 'parents'));
    }
    
    /**
     * Répondre à un message
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
        
        $reply = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $originalMessage->sender_id,
            'etablissement_id' => $user->etablissement_id,
            'eleve_id' => $originalMessage->eleve_id,
            'sujet' => 'RE: ' . $originalMessage->sujet,
            'message' => $request->message,
            'type' => 'admin_reply',
            'status' => 'envoye',
            'parent_message_id' => $originalMessage->id,
            'lu' => false,
        ]);
        
        $originalMessage->update(['status' => 'repondu']);
        
        return redirect()->route('etablissement.communications.show', $reply->id)
            ->with('success', 'Votre réponse a été envoyée.');
    }
    
    /**
     * Transfère un message au super admin
     */
    public function transferToSuperAdmin(Request $request, $id)
    {
        $originalMessage = Message::findOrFail($id);
        $user = Auth::user();
        
        $superAdmin = User::whereHas('role', function($q) {
            $q->where('name', 'super_admin');
        })->first();
        
        if (!$superAdmin) {
            return redirect()->back()->with('error', 'Super administrateur non trouvé.');
        }
        
        $messageTransfert = $request->message ?? 'Veuillez prendre connaissance de ce message.';
        
        $transfer = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $superAdmin->id,
            'etablissement_id' => $user->etablissement_id,
            'eleve_id' => $originalMessage->eleve_id,
            'sujet' => '[TRANSFERT] ' . $originalMessage->sujet,
            'message' => "Message original de: " . ($originalMessage->sender->name ?? 'Parent') . "\n\n" . $originalMessage->message . "\n\n--- Message de l'admin ---\n\n" . $messageTransfert,
            'type' => 'admin_to_superadmin',
            'status' => 'envoye',
            'parent_message_id' => $originalMessage->id,
            'lu' => false,
        ]);
        
        return redirect()->route('etablissement.communications.show', $transfer->id)
            ->with('success', 'Message transféré au super administrateur.');
    }
    
    /**
     * Supprime un message
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $message = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->findOrFail($id);
        
        $message->delete();
        
        return redirect()->route('etablissement.communications.index')
            ->with('success', 'Message supprimé avec succès.');
    }
    
    /**
     * Marquer comme lu
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
     * Marquer tous comme lus
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        Message::where('receiver_id', $user->id)
            ->where('type', 'parent_to_admin')
            ->where('lu', false)
            ->update(['lu' => true, 'lu_at' => now()]);
        
        return redirect()->back()->with('success', 'Tous les messages ont été marqués comme lus.');
    }
}