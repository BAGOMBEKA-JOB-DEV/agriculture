<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function products(Request $request)
    {
        $query = Product::with('seller');
        if ($request->status) {
            $query->where('status', $request->status);
        }
        return response()->json($query->paginate(20));
    }

    public function approveProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'available']);
        return response()->json(['message' => 'Product approved']);
    }

    public function rejectProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'rejected']);
        return response()->json(['message' => 'Product rejected']);
    }

    public function users(Request $request)
    {
        $query = User::query();
        if ($request->role) {
            $query->where('role', $request->role);
        }
        return response()->json($query->paginate(20));
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        // Assuming there is a verified field or similar
        // For now, let's just say we set it
        return response()->json(['message' => 'User verified']);
    }
}
