<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user first so ProductSeeder can find it
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'test@example.com',
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->call([
            ProductCategorySeeder::class,
            SubscriptionPlanSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
