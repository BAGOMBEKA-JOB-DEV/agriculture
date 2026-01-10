<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Rfq;
use App\Models\Payment;
use Illuminate\Http\Request;

use App\Jobs\NotifyGovernanceAction;

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

    public function users(Request $request)
    {
        $query = User::with(['sellerProfile', 'buyerProfile']);

        // Search Nexus (Name or Email)
        if ($request->filled('q')) {
            $search = '%' . $request->q . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', $search)
                  ->orWhere('email', 'ILIKE', $search);
            });
        }

        // Role Authority Filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Registry Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Jurisdiction Filter
        if ($request->filled('location')) {
            $query->where('location', 'ILIKE', '%' . $request->location . '%');
        }

        // Identity Verification Status
        if ($request->filled('verified')) {
            if ($request->verified === 'true') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->latest()->get();
        return response()->json($users);
    }

    /**
     * Promote a user to Super Admin.
     */
    public function promoteToSuperAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'super_admin';
        $user->save();

        NotifyGovernanceAction::dispatch($user, 'promote');

        return response()->json(['message' => 'User promoted to Super Admin successfully']);
    }

    /**
     * Mark a user as verified.
     */
    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = now();
        $user->status = 'active';
        $user->save();

        NotifyGovernanceAction::dispatch($user, 'verify');

        return response()->json(['message' => 'User verified successfully']);
    }

    /**
     * Suspend a user account (Revoke access).
     */
    public function suspendUser($id)
    {
        $user = User::findOrFail($id);
        // Only revoke if not already a super admin to prevent lockouts
        if ($user->role === 'super_admin') {
            return response()->json(['message' => 'Sovereign nodes cannot be revoked through standard protocols.'], 403);
        }
        
        $user->status = 'suspended';
        $user->save();

        NotifyGovernanceAction::dispatch($user, 'suspend');

        return response()->json(['message' => 'User account suspended successfully']);
    }

    /**
     * Activate a user account (Restore access).
     */
    public function activateUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        NotifyGovernanceAction::dispatch($user, 'activate');

        return response()->json(['message' => 'User account activated successfully']);
    }
}
