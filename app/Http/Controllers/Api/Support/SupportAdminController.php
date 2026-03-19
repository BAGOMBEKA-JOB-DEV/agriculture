<?php

namespace App\Http\Controllers\Api\Support;

use App\Http\Controllers\Controller;
use App\Models\Support\SupportTicket;
use App\Models\Support\SupportDepartment;
use App\Services\Support\TicketService;
use Illuminate\Http\Request;

class SupportAdminController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Dashboard statistics for agents.
     */
    public function getDashboardStats()
    {
        $dailyVolume = SupportTicket::select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get()
            ->reverse()
            ->values();

        $recentActivity = \App\Models\Support\SupportTicketHistory::with(['ticket', 'user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => ucfirst(str_replace('_', ' ', $item->action)),
                    'description' => "Ticket #{$item->ticket->ticket_number}: {$item->description}",
                    'time' => $item->created_at->diffForHumans(),
                    'bgClass' => 'bg-blue-50',
                    'icon' => 'svg-ticket',
                    'iconClass' => 'text-blue-600'
                ];
            });

        return response()->json([
            'tickets_by_status' => SupportTicket::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            'csat_score' => (float) (\App\Models\Support\SupportSatisfactionSurvey::avg('rating') ?? 0),
            'avg_resolution_time' => (float) (SupportTicket::whereNotNull('resolved_at')
                ->select(\Illuminate\Support\Facades\DB::raw('AVG(EXTRACT(EPOCH FROM (resolved_at - created_at)) / 3600) as avg_hours'))
                ->first()->avg_hours ?? 0),
            'volume_data' => $dailyVolume,
            'recent_activity' => $recentActivity,
        ]);
    }

    /**
     * List all tickets with filtering.
     */
    public function listTickets(Request $request)
    {
        $query = SupportTicket::with(['customer', 'category', 'agent', 'department'])
            ->orderByRaw("CASE WHEN priority = 'critical' THEN 1 WHEN priority = 'urgent' THEN 2 WHEN priority = 'high' THEN 3 WHEN priority = 'normal' THEN 4 ELSE 5 END")
            ->orderBy('updated_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('agent_id')) {
            $query->where('assigned_agent_id', $request->agent_id);
        }

        if ($request->boolean('unassigned')) {
            $query->whereNull('assigned_agent_id');
        }

        return response()->json($query->paginate(20));
    }

    /**
     * Show ticket details for admin.
     */
    public function showTicket($id)
    {
        $ticket = SupportTicket::with([
            'messages.user',
            'customer',
            'category',
            'agent',
            'department',
            'attachments',
            'history.user'
        ])->findOrFail($id);

        return response()->json($ticket);
    }

    /**
     * Assign agent to ticket.
     */
    public function assign(Request $request, $id)
    {
        $request->validate(['agent_id' => 'required|exists:users,id']);

        $ticket = $this->ticketService->assignTicket($id, $request->agent_id);

        return response()->json($ticket);
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:open,pending,on_hold,resolved,closed']);

        $ticket = $this->ticketService->updateStatus($id, $request->status);

        return response()->json($ticket);
    }

    /**
     * Send message or internal note.
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        $message = $this->ticketService->addMessage($id, [
            'body' => $request->body,
            'is_internal' => $request->is_internal ?? false,
            'message_type' => $request->is_internal ? 'note' : 'reply',
            'channel' => 'web',
        ]);

        return response()->json($message, 201);
    }
}
