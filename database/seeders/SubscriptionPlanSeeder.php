<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Vendor Weekly Plan',
                'slug' => 'vendor-weekly',
                'plan_type' => 'weekly',
                'user_role' => 'vendor',
                'price' => 10.00,
                'description' => 'Basic listing access for vendors - Weekly subscription',
                'features' => json_encode([
                    'List up to 10 products',
                    'Basic messaging',
                    'Product visibility for 7 days',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Vendor Monthly Plan',
                'slug' => 'vendor-monthly',
                'plan_type' => 'monthly',
                'user_role' => 'vendor',
                'price' => 35.00,
                'description' => 'Priority listing and messaging for vendors - Monthly subscription',
                'features' => json_encode([
                    'Unlimited product listings',
                    'Priority messaging',
                    'Featured listings',
                    'Analytics dashboard',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Buyer Weekly Plan',
                'slug' => 'buyer-weekly',
                'plan_type' => 'weekly',
                'user_role' => 'user',
                'price' => 5.00,
                'description' => 'Access to seller contacts and RFQ - Weekly subscription',
                'features' => json_encode([
                    'Browse all products',
                    'Contact sellers',
                    'Submit up to 5 RFQs',
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Buyer Monthly Plan',
                'slug' => 'buyer-monthly',
                'plan_type' => 'monthly',
                'user_role' => 'user',
                'price' => 15.00,
                'description' => 'Full access for buyers - Monthly subscription',
                'features' => json_encode([
                    'Browse all products',
                    'Unlimited seller contacts',
                    'Unlimited RFQ submissions',
                    'Priority support',
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\SubscriptionPlan::create($plan);
        }
    }
}
