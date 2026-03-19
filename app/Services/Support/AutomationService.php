<?php

namespace App\Services\Support;

use App\Models\Support\SupportAutomationRule;
use App\Models\Support\SupportTicket;
use Illuminate\Support\Facades\Log;

class AutomationService
{
    /**
     * Run automation rules for a specific event.
     */
    public function runRules(string $triggerType, SupportTicket $ticket)
    {
        $rules = SupportAutomationRule::where('trigger_type', $triggerType)
            ->where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();

        foreach ($rules as $rule) {
            if ($this->checkConditions($rule->conditions, $ticket)) {
                $this->executeActions($rule->actions, $ticket);
                $rule->increment('run_count');
                $rule->update(['last_run_at' => now()]);
            }
        }
    }

    /**
     * Check if ticket meets rule conditions.
     */
    protected function checkConditions(array $conditions, SupportTicket $ticket): bool
    {
        // Simple example logic: all conditions must be true
        foreach ($conditions as $condition) {
            $field = $condition['field'];
            $operator = $condition['operator'];
            $value = $condition['value'];

            $ticketValue = $ticket->{$field};

            switch ($operator) {
                case 'equals':
                    if ($ticketValue != $value)
                        return false;
                    break;
                case 'not_equals':
                    if ($ticketValue == $value)
                        return false;
                    break;
                case 'contains':
                    if (strpos((string) $ticketValue, $value) === false)
                        return false;
                    break;
            }
        }
        return true;
    }

    /**
     * Execute rule actions on the ticket.
     */
    protected function executeActions(array $actions, SupportTicket $ticket)
    {
        foreach ($actions as $action) {
            $type = $action['type'];
            $value = $action['value'];

            switch ($type) {
                case 'set_status':
                    $ticket->update(['status' => $value]);
                    break;
                case 'set_priority':
                    $ticket->update(['priority' => $value]);
                    break;
                case 'assign_to_agent':
                    $ticket->update(['assigned_agent_id' => $value]);
                    break;
                case 'assign_to_department':
                    $ticket->update(['department_id' => $value]);
                    break;
                case 'add_tag':
                    $tags = $ticket->tags ?? [];
                    if (!in_array($value, $tags)) {
                        $tags[] = $value;
                        $ticket->update(['tags' => $tags]);
                    }
                    break;
            }
        }
    }
}
