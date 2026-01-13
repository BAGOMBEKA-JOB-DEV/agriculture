<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RfqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Everyone can see ALL RFQs
        $rfqs = \App\Models\Rfq::with(['product', 'seller', 'buyer'])
            ->latest()
            ->get();
            
        return response()->json($rfqs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'delivery_location' => 'required|string',
            'preferred_date' => 'required|date|after:today',
            'additional_notes' => 'nullable|string',
        ]);

        $product = \App\Models\Product::findOrFail($request->product_id);

        // Prevent self-trading
        if ($product->seller_id === auth()->id()) {
             return response()->json(['message' => 'You cannot initiate a trade request for your own asset.'], 422);
        }

        $rfq = \App\Models\Rfq::create([
            'buyer_id' => auth()->id(),
            'seller_id' => $product->seller_id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'delivery_location' => $request->delivery_location,
            'preferred_date' => $request->preferred_date,
            'additional_notes' => $request->additional_notes,
            'status' => 'pending',
        ]);

        return response()->json($rfq, 201);
    }

    public function show($id)
    {
        $rfq = \App\Models\Rfq::with(['product', 'seller', 'buyer', 'messages.sender'])
            ->where(function($q) {
                $q->where('buyer_id', auth()->id())
                  ->orWhere('seller_id', auth()->id());
            })
            ->findOrFail($id);
            
        return response()->json($rfq);
    }

    public function update(Request $request, $id)
    {
        $rfq = \App\Models\Rfq::findOrFail($id);

        // Simple status update for seller response
        if (auth()->id() === $rfq->seller_id) {
            $request->validate([
                'seller_response' => 'required|string',
                'status' => 'required|in:responded,rejected,completed',
            ]);

            $rfq->update([
                'seller_response' => $request->seller_response,
                'status' => $request->status,
                'responded_at' => now(),
            ]);
        } else if (auth()->id() === $rfq->buyer_id) {
            // Buyer can only update initial details if still pending
            if ($rfq->status !== 'pending') {
                 return response()->json(['message' => 'Cannot update RFQ once responded'], 422);
            }
            $rfq->update($request->only(['quantity', 'delivery_location', 'preferred_date', 'additional_notes']));
        }

        return response()->json($rfq);
    }

    // Legacy message system (keeping for backward compatibility during transition)
    public function getMessages($id)
    {
        $rfq = \App\Models\Rfq::findOrFail($id);
        
        // Mark all messages sent TO current user as read
        $rfq->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        // Use rfq_messages table with attachments
        return response()->json($rfq->messages()->with(['sender', 'attachments'])->oldest()->get());
    }

    public function storeMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max per file
            'receiver_id' => 'nullable|exists:users,id',
        ]);

        if (!$request->message && !$request->hasFile('attachments')) {
            return response()->json(['message' => 'Message or attachment is required'], 422);
        }
        
        $rfq = \App\Models\Rfq::findOrFail($id);

        $message = $rfq->messages()->create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id, // Store key for private negotiation
            'message' => $request->message,
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('rfq-attachments', 'public');
                
                \App\Models\RfqMessageAttachment::create([
                    'rfq_message_id' => $message->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        // Auto-update status to 'responded' if it was pending
        if ($rfq->status === 'pending') {
            $rfq->update(['status' => 'responded']);
        }

        return response()->json($message->load(['sender', 'attachments']), 201);
    }

    // Get RFQ Conversations
    public function getConversations($rfqId)
    {
        $rfq = \App\Models\Rfq::findOrFail($rfqId);
        $user = auth()->user();
        
        // Get all messages for this RFQ involving the current user
        $messages = \App\Models\Message::where('rfq_id', $rfqId)
            ->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by conversation partner
        $conversations = $messages->groupBy(function($msg) use ($user) {
            return $msg->sender_id === $user->id ? $msg->receiver_id : $msg->sender_id;
        })->map(function($convMsgs, $partnerId) use ($user) {
            $lastMsg = $convMsgs->first();
            $partner = $lastMsg->sender_id === $user->id ? $lastMsg->receiver : $lastMsg->sender;
            
            return [
                'user' => $partner,
                'last_message' => $lastMsg,
                'unread_count' => $convMsgs->where('receiver_id', $user->id)->where('is_read', false)->count()
            ];
        })->values();
        
        return response()->json([
            'rfq' => $rfq->load(['product', 'buyer', 'seller']),
            'conversations' => $conversations
        ]);
    }

    public function destroy($id)
    {
        $rfq = \App\Models\Rfq::findOrFail($id);
        if (auth()->id() !== $rfq->buyer_id && !auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $rfq->delete();
        return response()->json(['message' => 'RFQ deleted successfully']);
    }
}
