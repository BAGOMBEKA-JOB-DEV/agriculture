<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rfq;
use Illuminate\Http\Request;

class RfqController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Rfq::with(['product', 'buyer', 'seller']);

        if ($user->hasRole('vendor')) {
            $query->where('seller_id', $user->id);
        } else {
            $query->where('buyer_id', $user->id);
        }

        $rfqs = $query->latest()->get();

        return response()->json($rfqs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'seller_id' => 'required|exists:users,id',
            'quantity' => 'required|numeric|min:0',
            'delivery_location' => 'required|string',
            'preferred_delivery_date' => 'required|date|after:today',
            'message' => 'nullable|string',
        ]);

        $rfq = Rfq::create([
            'buyer_id' => $request->user()->id,
            'seller_id' => $request->seller_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'delivery_location' => $request->delivery_location,
            'preferred_delivery_date' => $request->preferred_delivery_date,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return response()->json($rfq->load(['product', 'seller']), 201);
    }

    public function show(Rfq $rfq, Request $request)
    {
        $user = $request->user();

        if ($rfq->buyer_id !== $user->id && $rfq->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($rfq->load(['product', 'buyer', 'seller']));
    }

    public function update(Request $request, Rfq $rfq)
    {
        $user = $request->user();

        // Only seller can update status (respond)
        if ($rfq->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'response_message' => 'nullable|string',
        ]);

        $rfq->update([
            'status' => $request->status,
            'response_message' => $request->response_message,
            'responded_at' => now(),
        ]);

        return response()->json($rfq);
    }
}
