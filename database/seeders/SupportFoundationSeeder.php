<?php

namespace Database\Seeders;

use App\Models\Support\SupportBrand;
use App\Models\Support\SupportDepartment;
use App\Models\Support\SupportTicketCategory;
use Illuminate\Database\Seeder;

class SupportFoundationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Core Departments
        $billing = SupportDepartment::create([
            'name' => 'Billing & Finance',
            'description' => 'Handles invoices, payments, and subscriptions',
            'email' => 'billing@example.com'
        ]);

        $technical = SupportDepartment::create([
            'name' => 'Technical Support',
            'description' => 'General technical issues and platform troubleshooting',
            'email' => 'tech@example.com'
        ]);

        $sales = SupportDepartment::create([
            'name' => 'Sales',
            'description' => 'Product inquiries and enterprise licensing',
            'email' => 'sales@example.com'
        ]);

        // 2. Multi-Brand Configuration
        SupportBrand::updateOrCreate(
            ['subdomain' => 'support'],
            [
                'name' => 'Enterprise Main',
                'primary_color' => '#10B981',
                'settings' => ['theme' => 'light']
            ]
        );

        SupportBrand::updateOrCreate(
            ['subdomain' => 'pulse-beta'],
            [
                'name' => 'AgriPulse Beta',
                'primary_color' => '#3B82F6',
                'settings' => ['theme' => 'dark']
            ]
        );

        // 3. Ticket Categories with Auto-Assignment
        $accountTopic = SupportTicketCategory::create([
            'name' => 'Account Support',
            'icon' => 'user',
            'color' => '#3B82F6'
        ]);

        SupportTicketCategory::create([
            'name' => 'Password Reset',
            'parent_id' => $accountTopic->id,
            'auto_assign_to_department_id' => $technical->id,
            'color' => '#3B82F6'
        ]);

        $moneyTopic = SupportTicketCategory::create([
            'name' => 'Billing Queries',
            'icon' => 'credit-card',
            'color' => '#F59E0B'
        ]);

        SupportTicketCategory::create([
            'name' => 'Refund Request',
            'parent_id' => $moneyTopic->id,
            'auto_assign_to_department_id' => $billing->id,
            'color' => '#F59E0B'
        ]);

        SupportTicketCategory::create([
            'name' => 'Upgrade Subscription',
            'parent_id' => $moneyTopic->id,
            'auto_assign_to_department_id' => $sales->id,
            'color' => '#F59E0B'
        ]);

        // 4. Knowledge Base Categories & Articles
        $kbAccount = \App\Models\Support\SupportKbCategory::updateOrCreate(
            ['slug' => 'account-management'],
            [
                'name' => 'Account Management',
                'icon' => 'user',
                'is_public' => true
            ]
        );

        $kbLogistics = \App\Models\Support\SupportKbCategory::updateOrCreate(
            ['slug' => 'logistics-shipping'],
            [
                'name' => 'Logistics & Shipping',
                'icon' => 'truck',
                'is_public' => true
            ]
        );

        \App\Models\Support\SupportKbArticle::updateOrCreate(
            ['slug' => 'how-to-verify-corporate-status'],
            [
                'title' => 'How to verify your corporate status',
                'content' => 'To verify your corporate trader status, please upload your business registration documents in the settings panel.',
                'category_id' => $kbAccount->id,
                'author_id' => 1,
                'is_public' => true,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()
            ]
        );

        \App\Models\Support\SupportKbArticle::updateOrCreate(
            ['slug' => 'understanding-delivery-insurance'],
            [
                'title' => 'Understanding delivery insurance',
                'content' => 'All bulk grain transfers are insured up to $50,000 per shipment under our standard logistics agreement.',
                'category_id' => $kbLogistics->id,
                'author_id' => 1,
                'is_public' => true,
                'is_published' => true,
                'is_featured' => true,
                'published_at' => now()
            ]
        );
    }
}
