<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\Payment;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_vendors' => User::where('role', 'vendor')->count(),
            'total_buyers' => User::where('role', 'buyer')->count(),
            'total_traders' => User::where('role', 'trader')->count(),
            'total_products' => Product::count(),
            'total_rfqs' => Rfq::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_products' => Product::where('status', 'pending')->count(),
            'recent_users' => User::latest()->take(5)->get(),
        ]);
    }

    public function users()
    {
        $users = User::with(['sellerProfile', 'buyerProfile'])->latest()->get();
        return response()->json($users);
    }

    public function promoteToSuperAdmin($userId)
    {
        $user = User::findOrFail($userId);
        $user->role = 'super_admin';
        $user->save();

        return response()->json(['message' => 'User promoted to Super Admin successfully.']);
    }
}
