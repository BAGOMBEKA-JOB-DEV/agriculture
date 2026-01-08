<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/categories', function() {
    return (new CategoryController())->index();
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::put('/user/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/user/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Products
    Route::apiResource('products', ProductController::class);

    // RFQs
    Route::apiResource('rfqs', RfqController::class);

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

    // Admin Broadcast Center
    Route::get('/admin/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index']);
    Route::post('/admin/notifications/broadcast', [\App\Http\Controllers\Admin\NotificationController::class, 'store']);
    Route::get('/admin/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'show']);
    Route::put('/admin/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'update']);    // Admin
    Route::get('/admin/stats', [\App\Http\Controllers\AdminController::class, 'stats']);
    Route::get('/admin/products', [\App\Http\Controllers\AdminController::class, 'products']);
    Route::post('/admin/products/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveProduct']);
    Route::post('/admin/products/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectProduct']);
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
    
    // Super Admin Governance
    Route::middleware('role:super_admin')->prefix('super-admin')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\SuperAdminController::class, 'stats']);
        Route::get('/users', [\App\Http\Controllers\SuperAdminController::class, 'users']);
        Route::post('/users/{id}/promote', [\App\Http\Controllers\SuperAdminController::class, 'promoteToSuperAdmin']);
        
        // Category Management
        Route::apiResource('categories', CategoryController::class)->except(['index']);
    });
});
