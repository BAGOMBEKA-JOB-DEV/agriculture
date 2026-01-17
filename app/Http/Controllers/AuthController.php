<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use App\Jobs\ProcessPasswordResetMail;
use Illuminate\Support\Str;

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

        // --- 2FA Check ---
        if ($user->two_factor_confirmed_at) {
            // Log 2FA Requirement
            ActivityLog::create([
                'user_id' => $user->id,
                'event' => '2FA Challenge Initiated',
                'action' => 'Authentication',
                'level' => 'info',
                'user_name' => $user->name,
                'user_role' => $user->role,
                'module' => 'Auth',
                'status' => 'pending_mfa',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            if ($user->two_factor_type === 'email') {
                $code = rand(100000, 999999);
                \Illuminate\Support\Facades\Cache::put('2fa_code_' . $user->id, $code, now()->addMinutes(10));
                \App\Jobs\ProcessTwoFactorMail::dispatch($user, $code);
            }

            $email = $user->email;
            $type = $user->two_factor_type;

            Auth::logout();

            return response()->json([
                'two_factor_required' => true,
                'email' => $email,
                'two_factor_type' => $type
            ]);
        }

        // Log Successful Login (Immediate if no 2FA)
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

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'If this email exists in our records, you will receive a reset link shortly.'], 200);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]
        );

        ProcessPasswordResetMail::dispatch($user, $token);

        return response()->json(['message' => 'Password reset link sent to your email.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return response()->json(['message' => 'Invalid token or email.'], 422);
        }

        // Token expiry check (1 hour)
        if (now()->parse($resetRecord->created_at)->addHours(1)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'Password reset token has expired.'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        ActivityLog::create([
            'user_id' => $user->id,
            'event' => 'Password Reset',
            'action' => 'Authentication',
            'level' => 'info',
            'user_name' => $user->name,
            'user_role' => $user->role,
            'module' => 'Auth',
            'status' => 'success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        return response()->json(['message' => 'Your password has been reset successfully.']);
    }

    public function twoFactorChallenge(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->two_factor_confirmed_at) {
            return response()->json(['message' => 'Invalid request.'], 422);
        }

        $verified = false;

        // 1. Recovery Code Check
        if ($request->recovery_code) {
            $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
            if ($recoveryCodes && (($key = array_search($request->recovery_code, $recoveryCodes)) !== false)) {
                unset($recoveryCodes[$key]);
                $user->two_factor_recovery_codes = json_encode(array_values($recoveryCodes));
                $user->save();
                $verified = true;
            }
        } 
        // 2. TOTP App Check
        else if ($user->two_factor_type === 'app' && $request->code) {
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            if ($google2fa->verifyKey($user->two_factor_secret, $request->code)) {
                $verified = true;
            }
        }
        // 3. Email Check
        else if ($user->two_factor_type === 'email' && $request->code) {
            $storedCode = \Illuminate\Support\Facades\Cache::get('2fa_code_' . $user->id);
            if ($storedCode && $storedCode == $request->code) {
                \Illuminate\Support\Facades\Cache::forget('2fa_code_' . $user->id);
                $verified = true;
            }
        }

        if ($verified) {
            // Log Successful Verification
            ActivityLog::create([
                'user_id' => $user->id,
                'event' => '2FA Verification Success',
                'action' => 'Authentication',
                'level' => 'info',
                'user_name' => $user->name,
                'user_role' => $user->role,
                'module' => 'Auth',
                'status' => 'success',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            $token = $user->createToken('API Token', ['*'], now()->addMinutes(60))->plainTextToken;
            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'The provided two-factor authentication code was invalid.'
        ], 422);
    }
}
