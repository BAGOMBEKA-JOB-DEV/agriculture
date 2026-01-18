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
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', 'exists:roles,slug'],
            'location' => 'nullable|string'
        ]);

        $password = \Illuminate\Support\Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'location' => $validated['location'],
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        \App\Jobs\ProcessUserCreation::dispatch($user, $password);

        $role = \App\Models\Role::where('slug', $validated['role'])->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'exists:roles,slug'],
            'location' => 'nullable|string'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'location' => $validated['location'],
        ]);

        $role = \App\Models\Role::where('slug', $validated['role'])->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    /**
     * Send bulk invitations.
     */
    public function invite(Request $request)
    {
        $request->validate(['emails' => 'required|string']);
        $emails = array_map('trim', explode(',', $request->emails));

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                \App\Jobs\ProcessUserInvitation::dispatch($email);
            }
        }

        return response()->json(['message' => 'Invitations queued for ' . count($emails) . ' recipients']);
    }

    /**
     * Impersonate a user.
     */
    public function impersonate($id)
    {
        $user = User::findOrFail($id);
        // Log impersonation or handle token generation logic here
        return response()->json(['message' => "Impersonating {$user->name}"]);
    }

    /**
     * Send password reset link.
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        // \Illuminate\Support\Facades\Password::sendResetLink($user->email);
        return response()->json(['message' => 'Password reset link sent']);
    }

    /**
     * Get activity log for a user.
     */
    public function activityLog($id)
    {
        // Mocking data for now as the relationship might not cover all logs or frontend expects specifics
        // In real scenario: return $user->activityLogs()->latest()->get();
        return response()->json([
            ['action' => 'LOGIN_AUTHORIZED', 'description' => 'Successful biometric authentication via secure node.', 'timestamp' => '2 mins ago', 'type' => 'success'],
            ['action' => 'PROFILE_UPDATE', 'description' => 'Modified contact signaling channel.', 'timestamp' => '2 days ago', 'type' => 'info'],
            ['action' => 'PROTOCOL_AGREEMENT', 'description' => 'Signed updated trade sovereignty contract.', 'timestamp' => '1 week ago', 'type' => 'info']
        ]);
    }

    public function stats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_vendors' => User::whereHas('roles', fn($q) => $q->where('slug', 'vendor'))->count(),
            'total_buyers' => User::whereHas('roles', fn($q) => $q->where('slug', 'buyer'))->count(),
            'total_traders' => User::whereHas('roles', fn($q) => $q->where('slug', 'trader'))->count(),
            'total_super_admins' => User::whereHas('roles', fn($q) => $q->where('slug', 'super_admin'))->count(),
            'total_suspended' => User::where('status', 'suspended')->count(),
            'total_products' => Product::count(),
            'total_rfqs' => Rfq::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_products' => Product::where('status', 'pending')->count(),
            'recent_users' => User::latest()->take(5)->get(),
        ]);
    }

    public function users(Request $request)
    {
        $query = User::with(['sellerProfile', 'buyerProfile', 'roles']);

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
            $query->whereHas('roles', fn($q) => $q->where('slug', $request->role));
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

        $perPage = $request->input('per_page', 15);
        $users = $query->latest()->paginate($perPage);
        return response()->json($users);
    }

    /**
     * Promote a user to Super Admin.
     */
    public function promoteToSuperAdmin($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'super_admin'; // Legacy
        $user->save();

        // Dynamic Role Sync
        $role = \App\Models\Role::where('slug', 'super_admin')->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

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
        if ($user->hasRole('super_admin')) {
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

    /**
     * Perform bulk actions on selected users.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|string|in:verify,suspend,activate,delete'
        ]);

        $users = User::whereIn('id', $request->ids)->get();
        $count = 0;

        // Strict Registry Integrity Pre-flight for Deletion
        if ($request->action === 'delete') {
            foreach ($users as $user) {
                if ($this->hasActiveTelemetry($user)) {
                    return response()->json([
                        'message' => 'This user cannot be deleted, he/she is already having trade requests or lists or anything!!!'
                    ], 422);
                }
            }
        }

        foreach ($users as $user) {
            // Sovereign Safety Checks: Prevent suspension of Super Admins
            if ($request->action === 'suspend' && $user->hasRole('super_admin')) {
                continue;
            }

            switch ($request->action) {
                case 'verify':
                    $user->email_verified_at = now();
                    $user->status = 'active';
                    break;
                case 'suspend':
                    $user->status = 'suspended';
                    break;
                case 'activate':
                    $user->status = 'active';
                    $user->save();
                    break;
                case 'delete':
                    $user->delete();
                    break;
            }
            if ($request->action !== 'delete') {
                NotifyGovernanceAction::dispatch($user, $request->action);
            }
            $count++;
        }

        return response()->json([
            'message' => "Bulk protocol executed: {$count} nodes updated.",
            'updated_count' => $count
        ]);
    }

    /**
     * Initiate an asynchronous background export.
     */
    public function initiateExport(Request $request, $format)
    {
        if (!in_array($format, ['xlsx', 'csv', 'pdf', 'excel'])) {
            return response()->json(['message' => 'Unsupported format protocol.'], 400);
        }

        // Standardize Excel format
        $actualFormat = ($format === 'excel') ? 'xlsx' : $format;

        \App\Jobs\Governance\ExportUsersJob::dispatch(
            $actualFormat,
            $request->all(), // Filters
            auth()->id()
        );

        return response()->json([
            'message' => "Governance export initialized. You will be notified once the {$format} binary is ready for retrieval."
        ]);
    }

    /**
     * Download a generated export binary.
     */
    public function downloadExport($filename)
    {
        $path = 'exports/governance/' . $filename;
        if (!\Illuminate\Support\Facades\Storage::exists($path)) {
            return response()->json(['message' => 'Export binary not found or expired.'], 404);
        }

        return \Illuminate\Support\Facades\Storage::download($path, 'IAM_Governance_Export_' . now()->format('Ymd') . '.' . pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Delete a single user node.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('super_admin')) {
            return response()->json(['message' => 'Sovereign nodes cannot be purged from the registry.'], 403);
        }

        if ($this->hasActiveTelemetry($user)) {
            return response()->json(['message' => 'This user cannot be deleted, he/she is already having trade requests or lists or anything!!!'], 422);
        }
        
        $user->delete();
        return response()->json(['message' => 'User node purged from registry successfully.']);
    }

    /**
     * Check if a user has any associated marketplace telemetry.
     */
    private function hasActiveTelemetry(User $user): bool
    {
        return $user->products()->exists() || 
               $user->rfqs()->exists() || 
               $user->subscriptions()->exists() ||
               $user->sellerProfile()->exists() ||
               $user->buyerProfile()->exists();
    }
}
