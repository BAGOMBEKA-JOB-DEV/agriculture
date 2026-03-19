<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RBACSeeder extends Seeder
{
    public function run(): void
    {
        // Define Permissions
        $permissions = [
            'access_super_admin' => 'Access User Management',
            'access_admin' => 'Access Regulator Suite',
            'access_vendor' => 'Access Sales Suite',

            // Core Modules
            'view_dashboard' => 'View Personal Dashboard',
            'view_marketplace_products' => 'Browse Marketplace Products',
            'view_rfqs' => 'View RFQ Opportunities',
            'access_conversations' => 'Access Messaging System',
            'access_inbox' => 'Access Notification Inbox',
            'view_subscriptions' => 'View Subscription Plans',
            'manage_warehouse' => 'Manage Product Inventory',

            // Reporting Module (Granular)
            'view_reports_sales' => 'View Sales Reports',
            'view_reports_products' => 'View Product Performance',
            'view_reports_users' => 'View User Statistics',
            'view_reports_financial' => 'View Financial Reports',
            'view_reports_market' => 'View Market & Demand Analysis',
            'view_reports_trade_requests' => 'View Trade Requests',
            'view_reports_compliance' => 'View Compliance & Risks',
            'view_reports_system' => 'View System Health',

            // Admin Modules
            'manage_users' => 'Manage System Entities',
            'moderate_products' => 'Moderate Asset Registry',
            'broadcast_notifications' => 'Broadcast Global Notifications',
            'manage_categories' => 'Manage Asset Classifications',
            'manage_roles' => 'Manage Dynamic Role Ecosystem',

            // Global Network Hub
            'view_vendors' => 'View Vendor Directory',
            'view_traders' => 'View Trader Directory',
            'view_buyers' => 'View Buyer Directory',

            // AgriPulse Social Feed
            'access_agripulse' => 'Access AgriPulse Social Feed',
            'create_post' => 'Create AgriPulse Posts',

            // AgriPulse Moderation (Granular)
            'agripulse.moderate.posts' => 'Moderate AgriPulse Posts',
            'agripulse.moderate.comments' => 'Moderate AgriPulse Comments',
            'agripulse.moderate.users' => 'Manage AgriPulse User Warnings',
            'agripulse.moderate.reports' => 'Review AgriPulse Content Reports',
            'agripulse.moderate.analytics' => 'View AgriPulse Moderation Analytics',

            // Support Ecosystem (Enterprise Granularity)
            'support.tickets.view_all' => 'Full visibility into all support tickets across departments',
            'support.tickets.view_assigned' => 'Visibility into tickets assigned to user or their department',
            'support.tickets.create' => 'Ability to create new support requests',
            'support.tickets.reply' => 'Ability to participate in ticket conversations',
            'support.tickets.assign' => 'Ability to assign tickets to agents or departments',
            'support.tickets.manage_status' => 'Ability to update ticket states (Resolve/Close/Reopen)',
            'support.tickets.manage_priority' => 'Ability to escalate or downgrade ticket urgency',
            'support.tickets.delete' => 'Permanent removal of ticket records',
            'support.kb.manage' => 'Full control over knowledge base articles and information architecture',
            'support.settings.sla' => 'Configuration of Service Level Agreements and business schedules',
            'support.settings.automation' => 'Configuration of the support rules engine and automated workflows',
            'support.settings.brands' => 'Management of multi-brand support portals and aesthetics',
            'support.settings.canned_responses' => 'Management of productivity macros and templated replies',
            'support.reports.view' => 'Access to support performance diagnostics and agent metrics',
        ];

        foreach ($permissions as $slug => $description) {
            Permission::updateOrCreate(['slug' => $slug], [
                'name' => Str::title(str_replace(['_', '.'], ' ', $slug)),
                'description' => $description
            ]);
        }

        // Define Roles and their Permissions
        $roles = [
            'super_admin' => [
                'access_super_admin',
                'access_admin',
                'access_vendor',
                'view_dashboard',
                'view_marketplace_products',
                'view_rfqs',
                'access_conversations',
                'access_inbox',
                'view_subscriptions',
                'manage_warehouse',
                'manage_users',
                'moderate_products',
                'broadcast_notifications',
                'manage_categories',
                'manage_roles',
                'view_reports_sales',
                'view_reports_products',
                'view_reports_users',
                'view_reports_financial',
                'view_reports_market',
                'view_reports_trade_requests',
                'view_reports_compliance',
                'view_reports_system',
                'view_vendors',
                'view_traders',
                'view_buyers',
                'access_agripulse',
                'create_post',
                // AgriPulse Moderation
                'agripulse.moderate.posts',
                'agripulse.moderate.comments',
                'agripulse.moderate.users',
                'agripulse.moderate.reports',
                'agripulse.moderate.analytics',
                // Support Ecosystem (Full Access)
                'support.tickets.view_all',
                'support.tickets.view_assigned',
                'support.tickets.create',
                'support.tickets.reply',
                'support.tickets.assign',
                'support.tickets.manage_status',
                'support.tickets.manage_priority',
                'support.tickets.delete',
                'support.kb.manage',
                'support.settings.sla',
                'support.settings.automation',
                'support.settings.brands',
                'support.settings.canned_responses',
                'support.reports.view',
            ],
            'admin' => [
                'access_admin',
                'access_vendor',
                'view_dashboard',
                'view_marketplace_products',
                'view_rfqs',
                'access_conversations',
                'access_inbox',
                'view_subscriptions',
                'manage_warehouse',
                'moderate_products',
                'manage_users',
                'view_reports_sales',
                'view_reports_products',
                'view_reports_users',
                'view_reports_compliance',
                'view_vendors',
                'view_traders',
                'view_buyers',
                'access_agripulse',
                'create_post',
                // AgriPulse Moderation (limited)
                'agripulse.moderate.posts',
                'agripulse.moderate.comments',
                'agripulse.moderate.reports',
                // Support Ecosystem (Admin Access)
                'support.tickets.view_all',
                'support.tickets.view_assigned',
                'support.tickets.create',
                'support.tickets.reply',
                'support.tickets.assign',
                'support.tickets.manage_status',
                'support.tickets.manage_priority',
                'support.kb.manage',
                'support.settings.canned_responses',
                'support.reports.view',
            ],
            'vendor' => [
                'access_vendor',
                'view_dashboard',
                'view_marketplace_products',
                'view_rfqs',
                'access_conversations',
                'access_inbox',
                'view_subscriptions',
                'manage_warehouse',
                'view_reports_sales',
                'view_reports_products',
                'view_traders',
                'view_vendors',
                'view_buyers',  // Vendors can see potential trading partners
                'access_agripulse',
                'create_post',
                // Support (Create legacy)
                'support.tickets.create',
            ],
            'user' => [
                'view_dashboard',
                'view_marketplace_products',
                'view_rfqs',
                'access_conversations',
                'access_inbox',
                'view_subscriptions',
                'view_vendors',
                'access_agripulse',
                'create_post',
                // Support (Create legacy)
                'support.tickets.create',
            ],
            'support_agent' => [
                'view_dashboard',
                'access_conversations',
                'access_inbox',
                'support.tickets.view_assigned',
                'support.tickets.reply',
                'support.tickets.manage_status',
                'support.tickets.assign',
                'support.kb.manage',
            ],
            // Legacy fallbacks
            'buyer' => ['view_marketplace_products', 'view_rfqs', 'view_vendors', 'view_traders'],
            'trader' => ['view_marketplace_products', 'view_rfqs', 'view_vendors', 'view_buyers'],
        ];

        foreach ($roles as $slug => $rolePermissions) {
            $role = Role::updateOrCreate(['slug' => $slug], [
                'name' => Str::title(str_replace('_', ' ', $slug)),
                'description' => "Initial " . Str::title(str_replace('_', ' ', $slug)) . " Role"
            ]);

            $permissionIds = Permission::whereIn('slug', $rolePermissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
