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
            'access_super_admin' => 'Access Global Governance Nexus',
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
        ];

        foreach ($permissions as $slug => $description) {
            Permission::updateOrCreate(['slug' => $slug], [
                'name' => Str::title(str_replace('_', ' ', $slug)),
                'description' => $description
            ]);
        }

        // Define Roles and their Permissions
        $roles = [
            'super_admin' => [
                'access_super_admin', 'access_admin', 'access_vendor',
                'view_dashboard', 'view_marketplace_products', 'view_rfqs', 'access_conversations', 'access_inbox', 'view_subscriptions', 'manage_warehouse',
                'manage_users', 'moderate_products', 'broadcast_notifications', 'manage_categories', 'manage_roles',
                'view_reports_sales', 'view_reports_products', 'view_reports_users', 'view_reports_financial', 'view_reports_market', 'view_reports_trade_requests', 'view_reports_compliance', 'view_reports_system',
            ],
            'admin' => [
                'access_admin', 'access_vendor',
                'view_dashboard', 'view_marketplace_products', 'view_rfqs', 'access_conversations', 'access_inbox', 'view_subscriptions', 'manage_warehouse',
                'moderate_products', 'manage_users',
                'view_reports_sales', 'view_reports_products', 'view_reports_users', 'view_reports_compliance',
            ],
            'vendor' => [
                'access_vendor',
                'view_dashboard', 'view_marketplace_products', 'view_rfqs', 'access_conversations', 'access_inbox', 'view_subscriptions', 'manage_warehouse',
                'view_reports_sales', 'view_reports_products',
            ],
            'user' => [
                'view_dashboard', 'view_marketplace_products', 'view_rfqs', 'access_conversations', 'access_inbox', 'view_subscriptions',
            ],
            // Legacy fallbacks
            'buyer' => ['view_marketplace_products', 'view_rfqs'],
            'trader' => ['view_marketplace_products', 'view_rfqs'],
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
