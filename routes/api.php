<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    // Admin
    Route::get('/admin/stats', [\App\Http\Controllers\AdminController::class, 'stats']);
    Route::get('/admin/products', [\App\Http\Controllers\AdminController::class, 'products']);
    Route::post('/admin/products/{id}/approve', [\App\Http\Controllers\AdminController::class, 'approveProduct']);
    Route::post('/admin/products/{id}/reject', [\App\Http\Controllers\AdminController::class, 'rejectProduct']);
    Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users']);
    Route::post('/admin/users/{id}/verify', [\App\Http\Controllers\AdminController::class, 'verifyUser']);
});
