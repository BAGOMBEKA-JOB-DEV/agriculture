<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure Roles and Permissions exist
        $this->call(RBACSeeder::class);

        // Create or Update Super Admin User
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin', // Legacy column
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Get Super Admin Role
        $role = Role::where('slug', 'super_admin')->first();

        // Ensure role exists and attach
        if ($role) {
            // Ensure Role has ALL permissions
            $allPermissions = Permission::all();
            $role->permissions()->sync($allPermissions);

            // Sync User Role
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
