<?php

namespace App\Http\Controllers\Api\Support;

use App\Http\Controllers\Controller;
use App\Models\Support\SupportTicket;
use App\Models\Support\SupportTicketCategory;
use App\Models\Support\SupportKbArticle;
use App\Models\Support\SupportKbCategory;
use App\Services\Support\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * List user's tickets.
     */
    public function index(Request $request)
    {
        $tickets = SupportTicket::where('customer_id', auth()->id())
            ->with(['category', 'agent', 'department'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return response()->json($tickets);
    }

    /**
     * Create a new ticket.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:support_ticket_categories,id',
            'priority' => 'nullable|in:low,normal,high,urgent,critical',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = SupportTicketCategory::find($request->category_id);

        $ticket = $this->ticketService->createTicket([
            'subject' => $request->subject,
            'description' => $request->description,
            'customer_id' => auth()->id(),
            'category_id' => $request->category_id,
            'department_id' => $category->auto_assign_to_department_id,
            'priority' => $request->priority ?? 'normal',
            'source' => 'web',
        ]);

        // Add initial message
        $this->ticketService->addMessage($ticket->id, [
            'body' => $request->description,
            'message_type' => 'reply'
        ]);

        return response()->json($ticket, 201);
    }

    /**
     * Show ticket details with messages.
     */
    public function show($id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('customer_id', auth()->id())
            ->with(['messages.user', 'category', 'agent', 'attachments', 'history'])
            ->firstOrFail();

        return response()->json($ticket);
    }

    /**
     * Send a message on a ticket.
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $ticket = SupportTicket::where('id', $id)
            ->where('customer_id', auth()->id())
            ->firstOrFail();

        $message = $this->ticketService->addMessage($id, [
            'body' => $request->body,
            'is_internal' => false,
            'message_type' => 'reply',
            'channel' => 'web',
        ]);

        return response()->json($message, 201);
    }

    /**
     * Close a ticket.
     */
    public function close($id)
    {
        $ticket = SupportTicket::where('id', $id)
            ->where('customer_id', auth()->id())
            ->firstOrFail();

        $this->ticketService->updateStatus($id, 'closed');

        return response()->json(['message' => 'Ticket closed successfully']);
    }

    /**
     * List KB categories.
     */
    public function kbCategories()
    {
        $categories = SupportKbCategory::where('is_public', true)
            ->whereNull('parent_id')
            ->with('children')
            ->withCount([
                'articles' => function ($query) {
                    $query->where('is_published', true)->where('is_public', true);
                }
            ])
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }

    /**
     * List KB articles within a category.
     */
    public function kbArticles(Request $request)
    {
        $query = SupportKbArticle::where('is_published', true)
            ->where('is_public', true);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('is_featured')) {
            $query->where('is_featured', true);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        return response()->json($query->with('category')->paginate(20));
    }
}
