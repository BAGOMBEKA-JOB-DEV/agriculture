<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check if user has an active subscription
        if (!$user->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Active subscription required.',
                'code' => 'SUBSCRIPTION_REQUIRED'
            ], 403);
        }

        return $next($request);
    }
}
