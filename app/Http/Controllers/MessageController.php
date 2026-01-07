<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get all unique conversations for the user
        $messages = \App\Models\Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        $conversations = $messages->groupBy(function($message) use ($user) {
            return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
        })->map(function($convoMessages, $otherUserId) use ($user) {
            $lastMessage = $convoMessages->first();
            $otherUser = $lastMessage->sender_id === $user->id ? $lastMessage->receiver : $lastMessage->sender;
            
            return [
                'user' => $otherUser,
                'last_message' => $lastMessage
            ];
        })->values();

        return response()->json($conversations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $message = \App\Models\Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        return response()->json($message, 201);
    }

    public function show($conversationId)
    {
        $user = auth()->user();
        $messages = \App\Models\Message::where(function($query) use ($user, $conversationId) {
                $query->where('sender_id', $user->id)->where('receiver_id', $conversationId);
            })
            ->orWhere(function($query) use ($user, $conversationId) {
                $query->where('sender_id', $conversationId)->where('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function markAsRead($senderId)
    {
        $user = auth()->user();
        
        \App\Models\Message::where('sender_id', $senderId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'is_read' => true
            ]);

        return response()->json(['success' => true]);
    }
}
