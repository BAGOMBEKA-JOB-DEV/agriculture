<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = $request->user()
            ->subscriptions()
            ->with('plan')
            ->latest()
            ->get();

        return response()->json($subscriptions);
    }

    public function plans(Request $request)
    {
        $userRole = $request->user()->role;
        
        $plans = SubscriptionPlan::where('is_active', true)
            ->where('user_role', $userRole)
            ->get();

        return response()->json($plans);
    }

    public function subscribe(Request $request, SubscriptionPlan $plan)
    {
        $user = $request->user();

        // Check if user role matches plan
        if ($plan->user_role !== $user->role) {
            return response()->json(['message' => 'Invalid subscription plan for your role'], 400);
        }

        // Calculate subscription period
        $startsAt = now();
        $endsAt = $plan->plan_type === 'weekly' 
            ? $startsAt->copy()->addWeek()
            : $startsAt->copy()->addMonth();

        // Create subscription
        $subscription = $user->subscriptions()->create([
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        // Create payment record
        Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'status' => 'completed',
            'payment_method' => 'card',
            'paid_at' => now(),
        ]);

        return response()->json($subscription->load('plan'), 201);
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        // Ensure user owns the subscription
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return response()->json($subscription);
    }

    public function show(Subscription $subscription, Request $request)
    {
        // Ensure user owns the subscription
        if ($subscription->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($subscription->load('plan'));
    }
}
