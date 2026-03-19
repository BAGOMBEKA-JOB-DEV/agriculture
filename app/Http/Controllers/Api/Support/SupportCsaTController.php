<?php

namespace App\Http\Controllers\Api\Support;

use App\Http\Controllers\Controller;
use App\Models\Support\SupportTicket;
use App\Models\Support\SupportSatisfactionSurvey;
use Illuminate\Http\Request;

class SupportCsaTController extends Controller
{
    /**
     * Submit a CSAT survey for a ticket.
     */
    public function submit(Request $request, $ticketId)
    {
        $ticket = SupportTicket::where('id', $ticketId)
            ->where('customer_id', auth()->id())
            ->where('status', 'resolved') // Usually done on resolved tickets
            ->firstOrFail();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_text' => 'nullable|string',
        ]);

        $survey = SupportSatisfactionSurvey::updateOrCreate(
            ['ticket_id' => $ticketId],
            [
                'rating' => $request->rating,
                'feedback_text' => $request->feedback_text,
                'survey_type' => 'csat',
                'responded_at' => now(),
            ]
        );

        return response()->json(['message' => 'Thank you for your feedback!', 'survey' => $survey]);
    }
}
