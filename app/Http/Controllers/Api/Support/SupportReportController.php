<?php

namespace App\Http\Controllers\Api\Support;

use App\Http\Controllers\Controller;
use App\Models\Support\SupportTicket;
use App\Models\Support\SupportSatisfactionSurvey;
use Illuminate\Support\Facades\DB;

class SupportReportController extends Controller
{
    /**
     * Get support team performance reports.
     */
    public function getPerformanceReport()
    {
        $stats = [
            'total_tickets' => SupportTicket::count(),
            'avg_resolution_time' => $this->calculateAvgResolutionTime(),
            'csat_score' => $this->calculateCsatScore(),
            'tickets_by_status' => SupportTicket::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get(),
            'tickets_by_priority' => SupportTicket::select('priority', DB::raw('count(*) as total'))
                ->groupBy('priority')
                ->get(),
            'daily_volume' => SupportTicket::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get(),
        ];

        return response()->json($stats);
    }

    protected function calculateAvgResolutionTime()
    {
        $avgMinutes = SupportTicket::whereNotNull('resolved_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, created_at, resolved_at)) / 60 as avg_minutes'))
            ->first()->avg_minutes ?? 0;

        return round($avgMinutes, 2);
    }

    protected function calculateCsatScore()
    {
        $avg = SupportSatisfactionSurvey::whereNotNull('rating')
            ->avg('rating') ?? 0;

        return round($avg, 1);
    }
}
