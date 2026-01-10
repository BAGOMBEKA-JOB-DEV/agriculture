<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\BuyerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,slug',
            'user_type' => 'nullable|in:farmer,trader,company,buyer',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'user_type' => $request->user_type ?? ($request->role === 'vendor' ? 'farmer' : 'buyer'),
            'phone' => $request->phone,
            'location' => $request->location,
            'status' => 'pending',
        ]);

        // Dynamic Role Assignment
        $role = \App\Models\Role::where('slug', $request->role)->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        // Create profile based on role
        if ($user->hasRole('vendor')) {
            SellerProfile::create([
                'user_id' => $user->id,
                'business_name' => $request->name,
                'business_type' => $user->user_type,
            ]);
        } else {
            BuyerProfile::create([
                'user_id' => $user->id,
                'organization_name' => $request->name,
                'contact_person' => $request->name,
                'buyer_type' => 'local',
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load(['sellerProfile', 'buyerProfile']),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        // Check for active subscription
        $hasActiveSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();

        $user->has_active_subscription = $hasActiveSubscription;

        return response()->json([
            'user' => $user->load(['sellerProfile', 'buyerProfile']),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        $user = $request->user()->load(['sellerProfile', 'buyerProfile']);
        
        // Check for active subscription
        $hasActiveSubscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();

        $user->has_active_subscription = $hasActiveSubscription;

        return response()->json($user);
    }
}
