<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RfqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'vendor') {
            $rfqs = $user->rfqs()->with(['buyer', 'product'])->get();
        } else {
            $rfqs = $user->buyerProfile()->exists() || $user->role === 'buyer' 
                ? \App\Models\Rfq::where('buyer_id', $user->id)->with(['seller', 'product'])->get()
                : \App\Models\Rfq::with(['buyer', 'seller', 'product'])->get();
        }
        return response()->json($rfqs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'seller_id' => 'required|exists:users,id',
            'quantity' => 'required|numeric|min:0.01',
            'delivery_location' => 'required|string',
            'preferred_date' => 'required|date',
            'additional_notes' => 'nullable|string',
        ]);

        $rfq = \App\Models\Rfq::create([
            'buyer_id' => auth()->id(),
            'seller_id' => $request->seller_id,
            'product_id' => $request->product_id,
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
        $rfq = \App\Models\Rfq::with(['buyer', 'seller', 'product'])->findOrFail($id);
        
        // Authorization check
        if (auth()->id() !== $rfq->buyer_id && auth()->id() !== $rfq->seller_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

    public function destroy($id)
    {
        $rfq = \App\Models\Rfq::findOrFail($id);
        if (auth()->id() !== $rfq->buyer_id && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $rfq->delete();
        return response()->json(['message' => 'RFQ deleted successfully']);
    }
}
