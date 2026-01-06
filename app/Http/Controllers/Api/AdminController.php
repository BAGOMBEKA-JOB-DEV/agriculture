<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Rfq;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboardStats()
    {
        return response()->json([
            'total_users' => User::count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_rfqs' => Rfq::count(),
        ]);
    }

    public function users(Request $request)
    {
        $query = User::with(['sellerProfile', 'buyerProfile'])
            ->where('role', '!=', 'admin');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(20);

        return response()->json($users);
    }

    public function approveUser(User $user)
    {
        $user->update([
            'status' => 'active',
            'verified_at' => now(),
        ]);

        return response()->json(['message' => 'User approved successfully']);
    }

    public function suspendUser(User $user)
    {
        $user->update(['status' => 'suspended']);

        return response()->json(['message' => 'User suspended successfully']);
    }

    public function products(Request $request)
    {
        $query = Product::with(['seller', 'category']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(20);

        return response()->json($products);
    }

    public function approveProduct(Product $product)
    {
        $product->update(['status' => 'active']);

        return response()->json(['message' => 'Product approved successfully']);
    }

    public function rejectProduct(Product $product)
    {
        $product->update(['status' => 'rejected']);

        return response()->json(['message' => 'Product rejected successfully']);
    }
}
