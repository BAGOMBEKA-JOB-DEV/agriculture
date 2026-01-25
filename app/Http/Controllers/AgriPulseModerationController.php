<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\AgriPulsePostReport;
use App\Models\PostComment;
use App\Models\User;
use App\Models\AgriPulseUserModerationAction;
use App\Models\AgriPulseModerationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgriPulseModerationController extends Controller
{
    /**
     * Get overview statistics for the moderation dashboard.
     */
    public function dashboard()
    {
        return response()->json([
            'stats' => [
                'total_posts' => Post::count(),
                'active_posts' => Post::where('status', 'published')->count(),
                'total_comments' => PostComment::count(),
                'active_comments' => PostComment::where('status', 'published')->count(),
                'hidden_comments' => PostComment::where('status', 'hidden')->count(),
                'deleted_comments' => PostComment::onlyTrashed()->count(),
                'total_reports' => AgriPulsePostReport::count(),
                'pending_reports' => AgriPulsePostReport::where('status', 'pending')->count(),
                'resolved_reports' => AgriPulsePostReport::whereIn('status', ['resolved', 'dismissed'])->count(),
                'new_posts_today' => Post::whereDate('created_at', now()->today())->count(),
                'hidden_posts' => Post::where('status', 'hidden')->count(),
                'active_users' => User::whereHas('posts')->orWhereHas('postComments')->whereNotNull('email_verified_at')->where('status', 'active')->count(),
                'warned_users' => AgriPulseUserModerationAction::where('action_type', 'warn')->distinct('user_id')->count('user_id'),
                'total_likes' => \App\Models\PostReaction::where('type', 'like')->count(),
                'total_reposts' => Post::where('type', 'repost')->count(),
                'flagged_posts' => Post::where('status', 'flagged')->count(),
                'deleted_posts' => Post::onlyTrashed()->count(),
                'suspended_users' => User::whereNotNull('agripulse_banned_at')->count(),
                'total_users' => User::whereHas('posts')->orWhereHas('postComments')->count(),
                'total_logs' => AgriPulseModerationLog::count(),
                'logs_today' => AgriPulseModerationLog::whereDate('created_at', now()->today())->count(),
                'top_moderator' => User::whereHas('performedModerationLogs')
                    ->withCount('performedModerationLogs')
                    ->orderBy('performed_moderation_logs_count', 'desc')
                    ->first()?->name ?? 'None',
            ],
            'recent_logs' => AgriPulseModerationLog::with(['moderator', 'subject'])
                ->latest()
                ->limit(10)
                ->get(),
            'latest_reports' => AgriPulsePostReport::with(['reporter', 'post.user'])
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get()
        ]);
    }

    /**
     * List all users with moderation statistics.
     */
    public function getUsers(Request $request)
    {
        $query = User::withCount(['posts', 'receivedReports', 'moderationActions']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
                
                if (is_numeric($request->search)) {
                    $q->orWhere('id', $request->search);
                }
            });
        }

        return response()->json($query->latest()->paginate(20)->appends($request->all()));
    }

    /**
     * List all posts with moderation filters.
     */
    public function getPosts(Request $request)
    {
        $query = Post::with(['user', 'product'])
            ->withCount(['reports', 'reactions', 'comments'])
            ->withTrashed();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->deleted === 'true') {
            $query->onlyTrashed();
        }

        if ($request->search) {
            $query->where('content', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        return response()->json($query->latest()->paginate(20)->appends($request->all()));
    }

    /**
     * Hide a post from the public feed.
     */
    public function hidePost($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['status' => 'hidden']);
        
        $this->logModerationAction('hide_post', $post);

        return response()->json(['message' => 'Post hidden successfully', 'post' => $post]);
    }

    /**
     * Soft delete a post.
     */
    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        
        $this->logModerationAction('delete_post', $post);

        return response()->json(['message' => 'Post deleted successfully']);
    }

    /**
     * Restore a hidden or deleted post.
     */
    public function restorePost($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        
        if ($post->trashed()) {
            $post->restore();
        }
        
        $post->update(['status' => 'published']);
        
        $this->logModerationAction('restore_post', $post);

        return response()->json(['message' => 'Post restored successfully', 'post' => $post]);
    }

    /**
     * Toggle pin status of a post.
     */
    public function pinPost($id)
    {
        $post = Post::findOrFail($id);
        $post->is_pinned = !$post->is_pinned;
        $post->save();
        
        $action = $post->is_pinned ? 'pin_post' : 'unpin_post';
        $this->logModerationAction($action, $post);

        return response()->json([
            'message' => $post->is_pinned ? 'Post pinned successfully' : 'Post unpinned successfully', 
            'post' => $post
        ]);
    }

    /**
     * Flag a post for investigation.
     */
    public function flagPost($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['status' => 'flagged']);
        
        $this->logModerationAction('flag_post', $post);

        return response()->json(['message' => 'Post flagged for investigation', 'post' => $post]);
    }

    /**
     * List all reports.
     */
    public function getReports(Request $request)
    {
        $query = AgriPulsePostReport::with([
            'post' => function($q) {
                $q->withCount(['reactions', 'comments', 'reposts'])->with('user.roles');
            },
            'reporter.roles',
            'resolver'
        ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('reason', 'like', '%' . $request->search . '%')
                  ->orWhere('resolution_note', 'like', '%' . $request->search . '%')
                  ->orWhereHas('reporter', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        return response()->json($query->latest()->paginate(20)->appends($request->all()));
    }

    /**
     * Resolve a report.
     */
    public function resolveReport(Request $request, $id)
    {
        $report = AgriPulsePostReport::findOrFail($id);
        
        $report->update([
            'status' => 'resolved',
            'resolved_by' => auth()->id(),
            'resolution_note' => $request->note,
            'resolved_at' => now(),
        ]);

        $this->logModerationAction('resolve_report', $report, ['note' => $request->note]);

        return response()->json(['message' => 'Report resolved', 'report' => $report]);
    }

    /**
     * Dismiss a report.
     */
    public function dismissReport(Request $request, $id)
    {
        $report = AgriPulsePostReport::findOrFail($id);
        
        $report->update([
            'status' => 'dismissed',
            'resolved_by' => auth()->id(),
            'resolution_note' => $request->note,
            'resolved_at' => now(),
        ]);

        $this->logModerationAction('dismiss_report', $report, ['note' => $request->note]);

        return response()->json(['message' => 'Report dismissed', 'report' => $report]);
    }

    /**
     * List all comments with filters.
     */
    public function getComments(Request $request)
    {
        $query = PostComment::with(['user', 'post' => function($q) {
        $q->with('user')->withCount(['reactions', 'comments', 'reports']);
    }]);

        if ($request->search) {
            $query->where('content', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        return response()->json($query->latest()->paginate(20)->appends($request->all()));
    }

    /**
     * Delete a comment.
     */
    public function deleteComment($id)
    {
        $comment = PostComment::findOrFail($id);
        $comment->delete();
        
        $this->logModerationAction('delete_comment', $comment);

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    /**
     * Hide a comment.
     */
    public function hideComment($id)
    {
        $comment = PostComment::findOrFail($id);
        $comment->update(['status' => 'hidden']);
        
        $this->logModerationAction('hide_comment', $comment);

        return response()->json(['message' => 'Comment hidden successfully', 'comment' => $comment]);
    }

    /**
     * Restore a comment.
     */
    public function restoreComment($id)
    {
        $comment = PostComment::withTrashed()->findOrFail($id);
        
        if ($comment->trashed()) {
            $comment->restore();
        }
        
        $comment->update(['status' => 'published']);
        
        $this->logModerationAction('restore_comment', $comment);

        return response()->json(['message' => 'Comment restored successfully', 'comment' => $comment]);
    }

    /**
     * Get user's moderation history.
     */
    public function getUserModerationHistory($userId)
    {
        $user = User::withCount(['posts', 'receivedReports', 'moderationActions'])
            ->findOrFail($userId);
        
        // Moderation actions
        $actions = AgriPulseUserModerationAction::where('user_id', $userId)
            ->with('moderator')
            ->latest()
            ->get();
        
        // Reports against user's posts
        $reports = AgriPulsePostReport::whereHas('post', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['post', 'reporter'])->latest()->get();

        // Get user's posts with engagement metrics
        $posts = Post::where('user_id', $userId)
            ->withCount(['reactions', 'comments', 'reports'])
            ->latest()
            ->limit(10)
            ->get();

        // Calculate total engagement
        $totalReactions = Post::where('user_id', $userId)
            ->withCount('reactions')
            ->get()
            ->sum('reactions_count');
        
        $totalComments = Post::where('user_id', $userId)
            ->withCount('comments')
            ->get()
            ->sum('comments_count');

        // Post type breakdown
        $postBreakdown = [
            'total' => $user->posts_count,
            'with_images' => Post::where('user_id', $userId)->whereNotNull('image_url')->count(),
            'with_products' => Post::where('user_id', $userId)->whereNotNull('product_id')->count(),
            'text_only' => Post::where('user_id', $userId)->whereNull('image_url')->whereNull('product_id')->count(),
        ];

        // Status breakdown
        $statusBreakdown = [
            'published' => Post::where('user_id', $userId)->where('status', 'published')->count(),
            'hidden' => Post::where('user_id', $userId)->where('status', 'hidden')->count(),
            'flagged' => Post::where('user_id', $userId)->where('status', 'flagged')->count(),
            'deleted' => Post::where('user_id', $userId)->onlyTrashed()->count(),
        ];

        // Recent activity (posts and comments)
        $recentPosts = Post::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        $recentComments = PostComment::where('user_id', $userId)
            ->with('post')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'user' => $user,
            'actions' => $actions,
            'reports' => $reports,
            'posts' => $posts,
            'engagement' => [
                'total_reactions' => $totalReactions,
                'total_comments' => $totalComments,
                'avg_reactions_per_post' => $user->posts_count > 0 ? round($totalReactions / $user->posts_count, 1) : 0,
                'avg_comments_per_post' => $user->posts_count > 0 ? round($totalComments / $user->posts_count, 1) : 0,
            ],
            'post_breakdown' => $postBreakdown,
            'status_breakdown' => $statusBreakdown,
            'recent_posts' => $recentPosts,
            'recent_comments' => $recentComments,
        ]);
    }

    /**
     * Issue a warning to a user.
     */
    public function warnUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        $action = AgriPulseUserModerationAction::create([
            'user_id' => $userId,
            'moderator_id' => auth()->id(),
            'action_type' => 'warning',
            'reason' => $request->reason,
        ]);

        $this->logModerationAction('warn_user', $user, ['reason' => $request->reason]);

        return response()->json(['message' => 'User warned successfully', 'action' => $action]);
    }

    /**
     * Suspend a user temporarily.
     */
    public function suspendUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        
        $user->update(['agripulse_banned_at' => now()]);

        $action = AgriPulseUserModerationAction::create([
            'user_id' => $userId,
            'moderator_id' => auth()->id(),
            'action_type' => 'suspension',
            'reason' => $request->reason,
            'expires_at' => $request->expires_at ? now()->parse($request->expires_at) : null,
        ]);

        $this->logModerationAction('suspend_user', $user, [
            'reason' => $request->reason,
            'expires_at' => $request->expires_at
        ]);

        return response()->json(['message' => 'User suspended successfully', 'action' => $action]);
    }

    /**
     * Restore (activate) a suspended user.
     */
    public function activateUser($userId)
    {
        $user = User::findOrFail($userId);
        
        $user->update([
            'agripulse_banned_at' => null
        ]);

        $this->logModerationAction('activate_user', $user);

        return response()->json(['message' => 'User account restored successfully', 'user' => $user]);
    }

    /**
     * Get analytics for moderation.
     */
    public function getAnalytics(Request $request)
    {
        // Handle custom date range or period
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();
        } else {
            $days = $request->input('period', 30);
            $startDate = now()->subDays($days)->startOfDay();
            $endDate = now()->endOfDay();
        }
        
        $reportStatus = $request->input('status', 'all');
        
        // Post Trends
        $postTrends = Post::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Report Trends
        $reportQuery = AgriPulsePostReport::whereBetween('created_at', [$startDate, $endDate]);
        if ($reportStatus !== 'all') {
            $reportQuery->where('status', $reportStatus);
        }
        
        $reportTrends = $reportQuery
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Comment Trends
        $commentTrends = PostComment::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Reaction Trends
        $reactionTrends = \App\Models\PostReaction::where('type', 'like')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Calculate summary metrics
        $totalPosts = Post::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalComments = PostComment::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalReportsQuery = AgriPulsePostReport::whereBetween('created_at', [$startDate, $endDate]);
        if ($reportStatus !== 'all') {
            $totalReportsQuery->where('status', $reportStatus);
        }
        $totalReports = $totalReportsQuery->count();
        
        $resolvedReports = AgriPulsePostReport::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['resolved', 'dismissed'])
            ->count();

        $responseRate = $totalReports > 0 ? round(($resolvedReports / $totalReports) * 100) : 94;
        $resolutionRate = $totalReports > 0 ? round((AgriPulsePostReport::where('status', 'resolved')->count() / $totalReports) * 100) : 98;

        return response()->json([
            'post_trends' => $postTrends,
            'report_trends' => $reportTrends,
            'comment_trends' => $commentTrends,
            'reaction_trends' => $reactionTrends,
            'top_reported_users' => User::withCount([
                'posts' => function($q) {
                    $q->whereHas('reports');
                },
                'posts as all_posts_count'
            ])
            ->whereHas('posts.reports')
            ->orderBy('posts_count', 'desc')
            ->limit(5)
            ->get(),
            'summary' => [
                'total_posts' => $totalPosts,
                'total_comments' => $totalComments,
                'total_reports' => $totalReports,
                'resolved_reports' => $resolvedReports,
                'response_rate' => $responseRate,
                'resolution_rate' => $resolutionRate,
            ],
        ]);
    }

    /**
     * Get detailed information about a specific post for moderation.
     */
    public function getPostDetails($id)
    {
        $post = Post::with(['user', 'product'])
            ->withCount(['reactions', 'comments', 'reports'])
            ->findOrFail($id);

        // Get author's statistics
        $authorStats = [
            'total_posts' => Post::where('user_id', $post->user_id)->count(),
            'total_reactions' => Post::where('user_id', $post->user_id)
                ->withCount('reactions')
                ->get()
                ->sum('reactions_count'),
            'total_comments_received' => Post::where('user_id', $post->user_id)
                ->withCount('comments')
                ->get()
                ->sum('comments_count'),
            'reports_received' => AgriPulsePostReport::whereHas('post', function($q) use ($post) {
                $q->where('user_id', $post->user_id);
            })->count(),
        ];

        // Get top comments on this post
        $topComments = PostComment::where('post_id', $id)
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Get reports for this specific post
        $postReports = AgriPulsePostReport::where('post_id', $id)
            ->with(['reporter', 'resolver'])
            ->latest()
            ->get();

        // Get recent posts by same author
        $authorRecentPosts = Post::where('user_id', $post->user_id)
            ->where('id', '!=', $id)
            ->withCount(['reactions', 'comments', 'reports'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'post' => $post,
            'author_stats' => $authorStats,
            'top_comments' => $topComments,
            'post_reports' => $postReports,
            'author_recent_posts' => $authorRecentPosts,
        ]);
    }

    /**
     * Get detailed information about a specific comment for moderation.
     */
    public function getCommentDetails($id)
    {
        $comment = PostComment::with(['user', 'post' => function($q) {
            $q->with('user')->withCount(['reactions', 'comments', 'reports']);
        }])->findOrFail($id);

        // Get author's comment statistics
        $authorStats = [
            'total_comments' => PostComment::where('user_id', $comment->user_id)->count(),
            'total_posts' => Post::where('user_id', $comment->user_id)->count(),
            'account_age_days' => (int) max(0, $comment->user->created_at->diffInDays(now())),
            'account_age_hours' => (int) max(0, $comment->user->created_at->diffInHours(now())),
        ];

        // Get recent comments by same author
        $authorRecentComments = PostComment::where('user_id', $comment->user_id)
            ->where('id', '!=', $id)
            ->with(['post', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        // Get other comments on the same post
        $relatedComments = PostComment::where('post_id', $comment->post_id)
            ->where('id', '!=', $id)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'comment' => $comment,
            'author_stats' => $authorStats,
            'author_recent_comments' => $authorRecentComments,
            'related_comments' => $relatedComments,
        ]);
    }

    /**
     * Helper to log moderation actions.
     */
    /**
     * Get paginated activity logs.
     */
    public function getLogs(Request $request)
    {
        $query = AgriPulseModerationLog::with(['moderator', 'subject'])->latest();

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->moderator_id) {
            $query->where('moderator_id', $request->moderator_id);
        }

        if ($request->search) {
            $query->whereHas('moderator', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $logs = $query->paginate(20);
        
        // Append query parameters to pagination links
        $logs->appends($request->all());

        // Load authors for subjects
        $logs->getCollection()->each(function ($log) {
            if ($log->subject) {
                if ($log->subject_type === 'App\\Models\\Post' || $log->subject_type === 'App\\Models\\PostComment') {
                    $log->subject->load('user');
                }
            }
        });

        return response()->json($logs);
    }

    /**
     * Helper to log moderation actions.
     */
    private function logModerationAction($action, $subject, $metadata = [])
    {
        $metadata = $metadata ?: [];

        // Auto-capture subject details for richer logs
        if (isset($subject->content) || isset($subject->description)) {
            $content = $subject->content ?? $subject->description;
            $metadata['content_snippet'] = \Illuminate\Support\Str::limit($content, 50);
            $metadata['content_full'] = $content; // Store full content for detailed view
        }
        if (isset($subject->name)) {
            $metadata['subject_name'] = $subject->name;
        }
        if (isset($subject->reason)) {
            $metadata['report_reason'] = $subject->reason;
        }

        AgriPulseModerationLog::create([
            'moderator_id' => auth()->id(),
            'action' => $action,
            'subject_type' => get_class($subject),
            'subject_id' => $subject->id,
            'metadata' => $metadata,
        ]);
    }
    /**
     * Handle bulk actions for moderation resources.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'action' => 'required|string',
            'type' => 'required|string|in:post,comment,report,user,log',
            'reason' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');
        $type = $request->input('type');
        $reason = $request->input('reason');
        $note = $request->input('note');

        DB::beginTransaction();
        try {
            $processedCount = 0;

            switch ($type) {
                case 'post':
                    $posts = Post::whereIn('id', $ids)->get();
                    foreach ($posts as $post) {
                        if ($action === 'hide') {
                            $post->update(['status' => 'hidden']);
                            $this->logModerationAction('hide_post', $post, ['bulk' => true, 'reason' => $reason]);
                        }
                        if ($action === 'delete') {
                            $post->delete();
                            $this->logModerationAction('delete_post', $post, ['bulk' => true, 'reason' => $reason]);
                        }
                        if ($action === 'restore') {
                            if ($post->trashed()) $post->restore();
                            $post->update(['status' => 'published']);
                            $this->logModerationAction('restore_post', $post, ['bulk' => true, 'reason' => $reason]);
                        }
                        if ($action === 'flag') {
                            $post->update(['status' => 'flagged']);
                            $this->logModerationAction('flag_post', $post, ['bulk' => true, 'reason' => $reason]);
                        }
                        $processedCount++;
                    }
                    break;

                case 'comment':
                    $comments = PostComment::whereIn('id', $ids)->get();
                    foreach ($comments as $comment) {
                        if ($action === 'hide') {
                            $comment->update(['status' => 'hidden']);
                            $this->logModerationAction('hide_comment', $comment, ['bulk' => true, 'reason' => $reason]);
                        }
                        if ($action === 'delete') {
                            $comment->delete();
                            $this->logModerationAction('delete_comment', $comment, ['bulk' => true, 'reason' => $reason]);
                        }
                        if ($action === 'restore') {
                            if ($comment->trashed()) $comment->restore();
                            $comment->update(['status' => 'published']);
                            $this->logModerationAction('restore_comment', $comment, ['bulk' => true, 'reason' => $reason]);
                        }
                        $processedCount++;
                    }
                    break;

                case 'report':
                    $reports = AgriPulsePostReport::whereIn('id', $ids)->get();
                    foreach ($reports as $report) {
                        if ($action === 'resolve') {
                            $report->update([
                                'status' => 'resolved',
                                'resolved_by' => auth()->id(),
                                'resolved_at' => now(),
                                'resolution_note' => $note ?? 'Bulk resolution'
                            ]);
                            $this->logModerationAction('resolve_report', $report, ['bulk' => true, 'note' => $note ?? 'Bulk resolution']);
                        }
                        if ($action === 'dismiss') {
                            $report->update([
                                'status' => 'dismissed',
                                'resolved_by' => auth()->id(),
                                'resolved_at' => now(),
                                'resolution_note' => $note ?? 'Bulk dismissal'
                            ]);
                            $this->logModerationAction('dismiss_report', $report, ['bulk' => true, 'note' => $note ?? 'Bulk dismissal']);
                        }
                        $processedCount++;
                    }
                    break;
                
                case 'user':
                    $users = User::whereIn('id', $ids)->get();
                    foreach ($users as $user) {
                        if ($action === 'verify') {
                            $user->update(['email_verified_at' => now(), 'status' => 'active']);
                            $this->logModerationAction('verify_user', $user, ['bulk' => true]);
                        }
                        if ($action === 'suspend') {
                            $user->update(['agripulse_banned_at' => now()]);
                            // Also create a moderation action record for consistency
                            AgriPulseUserModerationAction::create([
                                'user_id' => $user->id,
                                'moderator_id' => auth()->id(),
                                'action_type' => 'suspension',
                                'reason' => $reason ?? 'Bulk suspension',
                                'expires_at' => null
                            ]);
                            $this->logModerationAction('suspend_user', $user, ['bulk' => true, 'reason' => $reason ?? 'Bulk suspension']);
                        }
                        if ($action === 'activate') {
                            $user->update(['agripulse_banned_at' => null]);
                            $this->logModerationAction('activate_user', $user, ['bulk' => true]);
                        }
                        $processedCount++;
                    }
                    break;

                case 'log':
                    if ($action === 'delete') {
                        $processedCount = AgriPulseModerationLog::whereIn('id', $ids)->delete();
                    }
                    break;
            }

            DB::commit();
            return response()->json(['message' => "Bulk action completed. processed $processedCount items."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Bulk action failed', 'error' => $e->getMessage()], 500);
        }
    }
}
