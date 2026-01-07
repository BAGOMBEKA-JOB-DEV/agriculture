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
    // Admin
    Route::get('/admin/stats', [\App\Http\Controllers\AdminController::class, 'stats']);
    Route::get('/admin/products', [\App\Http\Controllers\AdminController::class, 'products']);
    Route::post('/admin/products/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveProduct']);
    Route::post('/admin/products/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectProduct']);
    Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users']);
    Route::post('/admin/users/{id}/verify', [\App\Http\Controllers\AdminController::class, 'verifyUser']);

    // Super Admin Governance
    Route::middleware('role:super_admin')->prefix('super-admin')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\SuperAdminController::class, 'stats']);
        Route::get('/users', [\App\Http\Controllers\SuperAdminController::class, 'users']);
        Route::post('/users/{id}/promote', [\App\Http\Controllers\SuperAdminController::class, 'promoteToSuperAdmin']);
        
        // Category Management
        Route::apiResource('categories', CategoryController::class)->except(['index']);
    });
});
