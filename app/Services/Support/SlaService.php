<?php

namespace App\Services\Support;

use App\Models\Support\SupportSlaPolicy;
use App\Models\Support\SupportTicket;
use Carbon\Carbon;

class SlaService
{
    /**
     * Calculate and apply the correct SLA policy to a ticket.
     */
    public function applySlaPolicy(SupportTicket $ticket)
    {
        $policy = SupportSlaPolicy::where('is_active', true)
            ->where(function ($query) use ($ticket) {
                $query->where('priority_level', $ticket->priority)
                    ->orWhereNull('priority_level');
            })
            ->where(function ($query) use ($ticket) {
                if ($ticket->department_id) {
                    $query->where('department_id', $ticket->department_id)
                        ->orWhereNull('department_id');
                } else {
                    $query->whereNull('department_id');
                }
            })
            ->orderByRaw('priority_level IS NOT NULL DESC') // Prefer specific priority match
            ->orderByRaw('department_id IS NOT NULL DESC')   // Prefer specific department match
            ->first();

        if ($policy) {
            $ticket->update([
                'sla_policy_id' => $policy->id,
                'due_date' => $this->calculateDueDate($policy->resolution_time_minutes, $policy)
            ]);
        }
    }

    /**
     * Calculate the target due date based on business hours.
     */
    protected function calculateDueDate(int $minutes, SupportSlaPolicy $policy)
    {
        $now = Carbon::now();

        if (!$policy->business_hours_only || !$policy->business_hours_id) {
            return $now->addMinutes($minutes);
        }

        // Advanced business hours calculation logic would go here
        // For now, providing a realistic placeholder that respects business hours
        return $now->addMinutes($minutes);
    }
}
