<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class LinkExistingRolesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $roles = Role::all()->keyBy('slug');

        foreach ($users as $user) {
            if (isset($roles[$user->role])) {
                $user->roles()->syncWithoutDetaching([$roles[$user->role]->id]);
            }
        }
    }
}
