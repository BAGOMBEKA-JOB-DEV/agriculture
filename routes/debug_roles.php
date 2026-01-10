<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/debug-roles', function (Request $request) {
    $user = $request->user();
    if (!$user) {
        return response()->json(['error' => 'Not logged in'], 401);
    }
    
    // Refresh the user to ensure we get fresh data
    $user->load('roles');
    
    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'static_role_column' => $user->role ?? 'N/A',
        'dynamic_roles' => $user->roles->pluck('slug'),
        'has_super_admin' => $user->hasRole('super_admin'),
        'roles_raw' => $user->roles
    ]);
})->middleware('auth:sanctum');
