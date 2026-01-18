<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/categories', function() {
    return (new CategoryController())->index();
});

require __DIR__ . '/debug_roles.php';

Route::get('/marketplace', [ProductController::class, 'index'])->name('marketplace.index');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::put('/user/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/user/avatar', [AuthController::class, 'updateAvatar'])->middleware('auth:sanctum');
Route::post('/user/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

// 2FA Routes
Route::post('/two-factor/challenge', [AuthController::class, 'twoFactorChallenge']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Products
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);

    // RFQs
    Route::apiResource('rfqs', RfqController::class);
    Route::get('rfqs/{id}/conversations', [RfqController::class, 'getConversations']);
    Route::get('rfqs/{id}/messages', [RfqController::class, 'getMessages']);
    Route::post('rfqs/{id}/messages', [RfqController::class, 'storeMessage']);

    // Subscriptions
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/subscription/status', [SubscriptionController::class, 'status']);

    // Messages
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::get('/messages/{conversationId}', [MessageController::class, 'show']);
    Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::post('/messages/{id}/forward', [MessageController::class, 'forward']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::get('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'show']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);

    // Reporting & Analytics
    Route::prefix('reports')->group(function () {
        Route::get('/vendor', [\App\Http\Controllers\ReportingController::class, 'vendorStats']);
        Route::get('/buyer', [\App\Http\Controllers\ReportingController::class, 'buyerStats']);
        
        Route::middleware('permission:access_admin')->group(function () {
            Route::get('/admin', [\App\Http\Controllers\ReportingController::class, 'adminStats']);
        });
    });

    // Admin routes protected by permission middleware
    // Broadcast Center (requires broadcast_notifications permission)
    Route::middleware('permission:broadcast_notifications')->group(function () {
        Route::get('/admin/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index']);
        Route::post('/admin/notifications/broadcast', [\App\Http\Controllers\Admin\NotificationController::class, 'store']);
        Route::get('/admin/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'show']);
        Route::put('/admin/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'update']);
    });
    
    // Product Moderation (requires moderate_products permission)
    Route::middleware('permission:moderate_products')->group(function () {
        Route::get('/admin/stats', [\App\Http\Controllers\AdminController::class, 'stats']);
        Route::get('/admin/products', [\App\Http\Controllers\AdminController::class, 'products']);
        Route::post('/admin/products/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveProduct']);
        Route::post('/admin/products/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectProduct']);
    });
    
    // User Management (requires manage_users permission)
    Route::middleware('permission:manage_users')->group(function () {
        Route::get('/admin/roles', [\App\Http\Controllers\Admin\UserController::class, 'getRoles']);
        Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::post('/admin/users/bulk-action', [\App\Http\Controllers\Admin\UserController::class, 'bulkAction']);
        Route::post('/admin/users/invite', [\App\Http\Controllers\Admin\UserController::class, 'invite']);
        
        Route::get('/admin/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::put('/admin/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::delete('/admin/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy']);
        
        Route::post('/admin/users/{id}/suspend', [\App\Http\Controllers\Admin\UserController::class, 'suspend']);
        Route::post('/admin/users/{id}/activate', [\App\Http\Controllers\Admin\UserController::class, 'activate']);
        Route::post('/admin/users/{id}/impersonate', [\App\Http\Controllers\Admin\UserController::class, 'impersonate']);
        Route::post('/admin/users/{id}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword']);
        Route::post('/admin/users/{id}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verifyUser']);
    });
    
    // Super Admin Governance (requires access_super_admin permission)
    Route::middleware('permission:access_super_admin')->prefix('super-admin')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\SuperAdminController::class, 'stats']);
        Route::get('/users', [\App\Http\Controllers\SuperAdminController::class, 'users']);
        Route::post('/users/bulk', [\App\Http\Controllers\SuperAdminController::class, 'bulkAction']);
        Route::post('/users/export/{format}', [\App\Http\Controllers\SuperAdminController::class, 'initiateExport']);
        Route::get('/users/export/download/{filename}', [\App\Http\Controllers\SuperAdminController::class, 'downloadExport']);
        Route::post('/users/{id}/promote', [\App\Http\Controllers\SuperAdminController::class, 'promoteToSuperAdmin']);
        Route::post('/users/{id}/verify', [\App\Http\Controllers\SuperAdminController::class, 'verifyUser']);
        Route::post('/users/{id}/suspend', [\App\Http\Controllers\SuperAdminController::class, 'suspendUser']);
        Route::post('/users/{id}/activate', [\App\Http\Controllers\SuperAdminController::class, 'activateUser']);
        Route::post('/users', [\App\Http\Controllers\SuperAdminController::class, 'store']);
        Route::put('/users/{id}', [\App\Http\Controllers\SuperAdminController::class, 'update']);
        Route::post('/users/invite', [\App\Http\Controllers\SuperAdminController::class, 'invite']);
        Route::post('/users/{id}/impersonate', [\App\Http\Controllers\SuperAdminController::class, 'impersonate']);
        Route::post('/users/{id}/reset-password', [\App\Http\Controllers\SuperAdminController::class, 'resetPassword']);
        Route::get('/users/{id}/activity-log', [\App\Http\Controllers\SuperAdminController::class, 'activityLog']);
        Route::delete('/users/{id}', [\App\Http\Controllers\SuperAdminController::class, 'deleteUser']);
    });
    
    // Category Management (requires manage_categories permission)
    Route::middleware('permission:manage_categories')->group(function () {
        Route::apiResource('super-admin/categories', CategoryController::class)->except(['index']);
    });

    // Role Management (requires manage_roles permission)
    Route::middleware('permission:manage_roles')->group(function () {
        Route::apiResource('super-admin/roles', \App\Http\Controllers\Api\RoleController::class);
    });

    // Global Network Directory
    Route::prefix('network')->group(function () {
        Route::get('/vendors', [NetworkController::class, 'vendors']);
        Route::get('/traders', [NetworkController::class, 'traders']);
        Route::get('/buyers', [NetworkController::class, 'buyers']);
    });

    // 2FA Internal Management
    Route::prefix('two-factor')->group(function () {
        Route::post('/setup-app', [TwoFactorController::class, 'setupApp']);
        Route::post('/confirm-app', [TwoFactorController::class, 'confirmApp']);
        Route::post('/enable-email', [TwoFactorController::class, 'enableEmail']);
        Route::post('/disable', [TwoFactorController::class, 'disable']);
        Route::get('/recovery-codes', [TwoFactorController::class, 'getRecoveryCodes']);
        Route::post('/regenerate-recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes']);
    });
});
