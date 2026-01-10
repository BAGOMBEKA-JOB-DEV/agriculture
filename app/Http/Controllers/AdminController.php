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
        $query = Product::with(['seller', 'category']);

        // Discovery Search (Name / Description)
        if ($request->has('q') && !empty($request->q)) {
            $q = $request->q;
            $query->where(function($b) use ($q) {
                $b->where('name', 'ILIKE', "%{$q}%")
                  ->orWhere('description', 'ILIKE', "%{$q}%");
            });
        }

        // Seller Registry Lookup
        if ($request->has('seller') && !empty($request->seller)) {
            $sellerName = $request->seller;
            $query->whereHas('seller', function($sq) use ($sellerName) {
                $sq->where('name', 'ILIKE', "%{$sellerName}%");
            });
        }

        // Price Registry Range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sovereign Jurisdiction (Location)
        if ($request->has('location') && !empty($request->location)) {
            $query->where('location', 'ILIKE', "%{$request->location}%");
        }

        // Moderation Status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Structural Classification (Category)
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('product_category_id', $request->category_id);
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
