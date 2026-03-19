<?php

namespace App\Services\Support;

use App\Models\Support\SupportTicket;
use App\Models\Support\SupportTicketHistory;
use App\Models\Support\SupportTicketMessage;
use App\Models\Support\SupportTicketCategory;
use App\Services\Support\SlaService;
use App\Services\Support\AutomationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketService
{
    protected $slaService;
    protected $automationService;

    public function __construct(SlaService $slaService, AutomationService $automationService)
    {
        $this->slaService = $slaService;
        $this->automationService = $automationService;
    }
    /**
     * Create a new ticket with automatic number generation and initial history.
     */
    public function createTicket(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['ticket_number'] = $this->generateTicketNumber();

            $ticket = SupportTicket::create($data);

            // Apply SLA Policy
            $this->slaService->applySlaPolicy($ticket);

            // Run Automation Rules
            $this->automationService->runRules('ticket_created', $ticket);

            $this->logHistory($ticket->id, auth()->id(), 'created', null, null, 'Ticket created via ' . ($data['source'] ?? 'web'));

            return $ticket;
        });
    }

    /**
     * Progress the ticket status with history logging.
     */
    public function updateStatus(int $ticketId, string $status, ?int $userId = null)
    {
        return DB::transaction(function () use ($ticketId, $status, $userId) {
            $ticket = SupportTicket::findOrFail($ticketId);
            $oldStatus = $ticket->status;

            if ($oldStatus === $status)
                return $ticket;

            $ticket->update(['status' => $status]);

            $this->logHistory($ticketId, $userId ?? auth()->id(), 'status_changed', 'status', $oldStatus, $status);

            if ($status === 'resolved') {
                $ticket->update(['resolved_at' => now()]);
            } elseif ($status === 'closed') {
                $ticket->update(['closed_at' => now()]);
            }

            return $ticket;
        });
    }

    /**
     * Assign ticket to an agent.
     */
    public function assignTicket(int $ticketId, ?int $agentId, ?int $userId = null)
    {
        return DB::transaction(function () use ($ticketId, $agentId, $userId) {
            $ticket = SupportTicket::findOrFail($ticketId);
            $oldAgentId = $ticket->assigned_agent_id;

            $ticket->update(['assigned_agent_id' => $agentId]);

            $this->logHistory(
                $ticketId,
                $userId ?? auth()->id(),
                'assigned',
                'assigned_agent_id',
                $oldAgentId,
                $agentId,
                $agentId ? 'Ticket assigned to agent' : 'Ticket unassigned'
            );

            return $ticket;
        });
    }

    /**
     * Add a message/reply to a ticket.
     */
    public function addMessage(int $ticketId, array $messageData)
    {
        return DB::transaction(function () use ($ticketId, $messageData) {
            $ticket = SupportTicket::findOrFail($ticketId);

            $message = SupportTicketMessage::create([
                'ticket_id' => $ticketId,
                'user_id' => auth()->id(),
                'message' => $messageData['body'],
                'is_internal' => $messageData['is_internal'] ?? false,
                'message_type' => $messageData['message_type'] ?? 'reply',
                'channel' => $messageData['channel'] ?? 'web',
            ]);

            // Update ticket status based on sender
            if (!$message->is_internal && auth()->id() !== $ticket->customer_id) {
                // Staff replied, status becomes pending (waiting for customer)
                $ticket->update(['status' => 'pending']);
            } elseif (!$message->is_internal && auth()->id() === $ticket->customer_id) {
                // Customer replied to a pending/resolved ticket, move back to open
                if (in_array($ticket->status, ['pending', 'resolved'])) {
                    $ticket->update(['status' => 'open']);
                }
            }

            return $message;
        });
    }

    /**
     * Log an entry in the ticket history.
     */
    protected function logHistory(int $ticketId, ?int $userId, string $action, ?string $field = null, $oldValue = null, $newValue = null, ?string $description = null)
    {
        SupportTicketHistory::create([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'action' => $action,
            'field_name' => $field,
            'old_value' => (string) $oldValue,
            'new_value' => (string) $newValue,
            'description' => $description,
        ]);
    }

    /**
     * Generate a unique ticket number.
     */
    protected function generateTicketNumber(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        return "TKT-{$date}-{$random}";
    }
}
