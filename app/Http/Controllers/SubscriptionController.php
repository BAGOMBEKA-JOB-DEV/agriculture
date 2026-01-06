<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function status()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription()->with('plan')->first();
        
        return response()->json([
            'is_subscribed' => $subscription ? true : false,
            'subscription' => $subscription,
            'plan' => $subscription ? $subscription->plan : null,
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = auth()->user();
        
        // Deactivate existing subscriptions
        $user->subscriptions()->update(['status' => 'expired']);

        $subscription = \App\Models\Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $request->plan_id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        return response()->json($subscription, 201);
    }
}
