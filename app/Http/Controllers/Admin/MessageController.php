<?php
// app/Http/Controllers/Admin/MessageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Affiche la messagerie du SUPER ADMIN
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Messages reçus des admins d'établissement
        $messagesRecus = Message::where('receiver_id', $user->id)
            ->where('type', 'admin_to_superadmin')
            ->with(['sender', 'sender.etablissement', 'replies'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Messages envoyés par le super admin
        $messagesEnvoyes = Message::where('sender_id', $user->id)
            ->where('type', 'superadmin_reply')
            ->with(['receiver', 'receiver.etablissement'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques
        $stats = [
            'non_lus' => Message::where('receiver_id', $user->id)
                ->where('type', 'admin_to_superadmin')
                ->where('lu', false)
                ->count(),
            'total_recus' => Message::where('receiver_id', $user->id)
                ->where('type', 'admin_to_superadmin')
                ->count(),
            'total_envoyes' => Message::where('sender_id', $user->id)
                ->where('type', 'superadmin_reply')
                ->count(),
            'aujourd_hui' => Message::where('receiver_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
        ];
        
        // Liste des admins d'établissement
        $admins = User::whereHas('role', function($q) {
            $q->where('name', 'admin_etablissement');
        })->with('etablissement')->get();
        
        return view('admin.communications.index', compact(
            'messagesRecus', 'messagesEnvoyes', 'stats', 'admins'
        ));
    }
    
    /**
     * Envoie un message à un admin
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string|min:5',
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
            'sujet' => $request->sujet,
            'message' => $request->message,
            'type' => 'superadmin_reply',
            'status' => 'envoye',
            'lu' => false,
        ]);
        
        return redirect()->route('admin.communications.show', $message->id)
            ->with('success', 'Message envoyé avec succès.');
    }
    
    /**
     * Affiche un message
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $message = Message::with(['sender', 'sender.etablissement', 'receiver', 'receiver.etablissement', 'replies.sender'])
            ->findOrFail($id);
        
        if ($message->receiver_id != $user->id && $message->sender_id != $user->id) {
            abort(403);
        }
        
        if ($message->receiver_id == $user->id && !$message->lu) {
            $message->update(['lu' => true, 'lu_at' => now()]);
        }
        
        $admins = User::whereHas('role', function($q) {
            $q->where('name', 'admin_etablissement');
        })->with('etablissement')->get();
        
        return view('admin.communications.show', compact('message', 'admins'));
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
            'sujet' => 'RE: ' . $originalMessage->sujet,
            'message' => $request->message,
            'type' => 'superadmin_reply',
            'status' => 'envoye',
            'parent_message_id' => $originalMessage->id,
            'lu' => false,
        ]);
        
        $originalMessage->update(['status' => 'repondu']);
        
        return redirect()->route('admin.communications.show', $reply->id)
            ->with('success', 'Votre réponse a été envoyée.');
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
        
        return redirect()->route('admin.communications.index')
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
            ->where('type', 'admin_to_superadmin')
            ->where('lu', false)
            ->update(['lu' => true, 'lu_at' => now()]);
        
        return redirect()->back()->with('success', 'Tous les messages ont été marqués comme lus.');
    }
}