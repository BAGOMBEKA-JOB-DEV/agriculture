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
            ->with(['sender', 'receiver', 'attachments'])
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
            'content' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip|max:10240',
        ]);

        if (!$request->content && !$request->hasFile('attachments')) {
            return response()->json(['message' => 'Message must contain text or attachments.'], 422);
        }

        $message = \App\Models\Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content ?? '',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message-attachments', 'public');
                
                \App\Models\MessageAttachment::create([
                    'message_id' => $message->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return response()->json($message->load(['sender', 'receiver', 'attachments']), 201);
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
            ->with(['sender', 'receiver', 'attachments'])
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

    public function forward(Request $request, $id)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        $originalMessage = \App\Models\Message::with('attachments')->findOrFail($id);
        
        // Ensure user has access to this message (sender or receiver)
        if ($originalMessage->sender_id !== auth()->id() && $originalMessage->receiver_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $newMessage = \App\Models\Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $originalMessage->content, // Or prepend "Forwarded: "
        ]);

        foreach ($originalMessage->attachments as $attachment) {
            \App\Models\MessageAttachment::create([
                'message_id' => $newMessage->id,
                'file_path' => $attachment->file_path,
                'file_name' => $attachment->file_name,
                'file_type' => $attachment->file_type,
                'file_size' => $attachment->file_size,
            ]);
        }

        return response()->json($newMessage->load(['sender', 'receiver', 'attachments']), 201);
    }

    public function destroy($id)
    {
        $message = \App\Models\Message::with('attachments')->findOrFail($id);

        if ($message->sender_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Remove attachments files to save space
        foreach ($message->attachments as $attachment) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $message->update([
            'is_deleted' => true,
            'content' => '', // Clear content
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }
}
