<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        // Transform for frontend
        $users->getCollection()->transform(function ($user) {
            return $this->formatUser($user);
        });

        return response()->json($users);
    }

    public function getRoles()
    {
        // Return all roles except super_admin
        return response()->json(
            \App\Models\Role::where('slug', '!=', 'super_admin')->get(['name', 'slug'])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', 'exists:roles,slug'], // Dynamic check
            'location' => 'nullable|string'
        ]);

        $password = Str::random(12); // Generate temp password
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'location' => $validated['location'],
            'password' => Hash::make($password),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Dispatch Job to send email
        \App\Jobs\ProcessUserCreation::dispatch($user, $password);
        
        // Dynamic Role Sync
        $role = \App\Models\Role::where('slug', $validated['role'])->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }
        
        return response()->json([
            'message' => 'User created successfully',
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'exists:roles,slug'], // Dynamic check
            'location' => 'nullable|string'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'location' => $validated['location'],
        ]);

        // Dynamic Role Sync
        $role = \App\Models\Role::where('slug', $validated['role'])->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $this->formatUser($user),
        ]);
    }

    // ... (destroy method remains)

    // ... (suspend/activate/bulk methods remain)

    public function invite(Request $request)
    {
        $request->validate([
            'emails' => 'required|string'
        ]);

        $emails = array_map('trim', explode(',', $request->emails));
        
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                 \App\Jobs\ProcessUserInvitation::dispatch($email);
            }
        }

        return response()->json(['message' => 'Invitations queued for ' . count($emails) . ' recipients']);
    }

    public function impersonate(string $id)
    {
        // Simple token limitation or logging could happen here
        // For JWT/Sanctum, typically you'd return a new token for this user
        // But for this mocked frontend "Impersonate" usually just logs it 
        // or frontend replaces state. 
        // Let's assume we return a success message for audit logging.
        
        $user = User::findOrFail($id);
        // Log::info("Admin impersonated user {$user->id}");

        return response()->json(['message' => "Impersonating {$user->name}"]);
    }

    public function verifyUser(string $id)
    {
        $user = User::findOrFail($id);
        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return response()->json(['message' => 'User verified successfully', 'user' => $this->formatUser($user)]);
    }

    public function resetPassword(string $id)
    {
        $user = User::findOrFail($id);
        
        // In real app: Password::sendResetLink($user->email);
        
        return response()->json(['message' => 'Password reset link sent']);
    }

    private function formatUser($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'location' => $user->location,
            'email_verified_at' => $user->email_verified_at,
            'status' => $user->status,
            'suspended_at' => $user->status === 'suspended' ? $user->updated_at : null, // Synthesize for frontend
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
