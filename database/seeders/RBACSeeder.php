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
            ],
            'admin' => [
                'access_admin', 'access_vendor',
                'view_dashboard', 'view_marketplace_products', 'view_rfqs', 'access_conversations', 'access_inbox', 'view_subscriptions', 'manage_warehouse',
                'moderate_products', 'manage_users',
            ],
            'vendor' => [
                'access_vendor',
                'view_dashboard', 'view_marketplace_products', 'view_rfqs', 'access_conversations', 'access_inbox', 'view_subscriptions', 'manage_warehouse',
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
