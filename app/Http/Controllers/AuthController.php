<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string|in:admin,vendor,buyer,trader',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?: 'buyer',
        ]);

        // Attach Dynamic Role
        $roleSlug = $request->role ?: 'buyer';
        $role = \App\Models\Role::where('slug', $roleSlug)->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        // Dispatch Welcome Email Job
        \App\Jobs\ProcessWelcomeRegistration::dispatch($user);

        return response()->json([
            'user' => $user,
            'message' => 'Account created successfully. Please login to continue.'
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            // Log Failed Login
            ActivityLog::create([
                'event' => 'Login Attempt',
                'action' => 'Authentication',
                'level' => 'warning',
                'user_name' => $request->email,
                'module' => 'Auth',
                'status' => 'failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // Enforce Account Revocation
        if ($user->status === 'suspended') {
            ActivityLog::create([
                'user_id' => $user->id,
                'event' => 'Login Attempt',
                'action' => 'Authentication',
                'level' => 'error',
                'user_name' => $user->name,
                'user_role' => $user->role,
                'module' => 'Auth',
                'status' => 'suspended',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);
            Auth::logout();
            return response()->json([
                'message' => 'Please your account has been revoked! Contact support for any more help'
            ], 403);
        }

        // Log Successful Login
        ActivityLog::create([
            'user_id' => $user->id,
            'event' => 'User Login',
            'action' => 'Authentication',
            'level' => 'info',
            'user_name' => $user->name,
            'user_role' => $user->role,
            'module' => 'Auth',
            'status' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        // Dispatch Security Alert Job
        \App\Jobs\ProcessLoginSecurityAlert::dispatch(
            $user, 
            $request->ip(), 
            $request->header('User-Agent'), 
            now()->format('M d, Y H:i:s T')
        );

        // Create token that expires in 1 hour (60 minutes)
        $token = $user->createToken('API Token', ['*'], now()->addMinutes(60))->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        
        ActivityLog::create([
            'user_id' => $user->id,
            'event' => 'User Logout',
            'action' => 'Authentication',
            'level' => 'info',
            'user_name' => $user->name,
            'user_role' => $user->role,
            'module' => 'Auth',
            'status' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user()->load('roles.permissions'));
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'notification_preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'location' => $request->location,
            'notification_preferences' => $request->notification_preferences,
        ]);

        return response()->json($user);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Current password is incorrect']]], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);

            return response()->json([
                'user' => $user->fresh(),
                'message' => 'Profile picture updated successfully'
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }
}
