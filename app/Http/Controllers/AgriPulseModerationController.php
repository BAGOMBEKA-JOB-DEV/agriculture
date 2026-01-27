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

use App\Jobs\AgriPulse\SendModerationNotification;

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

        // Notify Author
        $contentSnippet = \Illuminate\Support\Str::limit($post->content, 50);
        $this->dispatchModerationNotification(
            $post->user,
            'Your Post Was Hidden',
            "We wanted to let you know that your post from {$post->created_at->format('M d, Y')} containing \"{$contentSnippet}\" has been temporarily hidden. This action was taken to allow our moderation team to review it against our community guidelines regarding safe and appropriate content. You will be notified once a final decision is made.",
            'high',
            "/agripulse/post/{$post->id}",
            $post->media_url ?? null, // Assuming media_url exists or similar
            [
                'post_content' => $post->content,
                'post_date' => $post->created_at->toIso8601String(),
                'hidden_at' => now()->toIso8601String(),
                'metrics' => [
                    'reactions' => $post->reactions()->count(),
                    'comments' => $post->comments()->count(),
                    'reports' => $post->reports()->count(),
                ],
                'author' => [
                    'name' => $post->user->name,
                    'total_posts' => $post->user->posts()->count(),
                    'joined_at' => $post->user->created_at->toIso8601String(),
                ]
            ]
        );

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

        // Notify Author
        $contentSnippet = \Illuminate\Support\Str::limit($post->content, 50);
        $this->dispatchModerationNotification(
            $post->user,
            'Your Post Was Removed',
            "We regret to inform you that your post from {$post->created_at->format('M d, Y')} containing \"{$contentSnippet}\" has been permanently removed from AgriPulse. This decision was made because the content was found to be in violation of our Terms of Service or Community Standards. Please review our guidelines to avoid future infractions.",
            'critical',
            "/agripulse/post/{$post->id}",
            $post->image_url ?? null,
            [
                'post_content' => $post->content,
                'post_date' => $post->created_at->toIso8601String(),
                'removed_at' => now()->toIso8601String(),
                'metrics' => [
                    'reactions' => $post->reactions()->count(),
                    'comments' => $post->comments()->count(),
                    'reports' => $post->reports()->count(),
                ],
                'author' => [
                    'name' => $post->user->name,
                    'total_posts' => $post->user->posts()->count(),
                    'joined_at' => $post->user->created_at->toIso8601String(),
                ]
            ]
        );

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

        // Notify Author
        $contentSnippet = \Illuminate\Support\Str::limit($post->content, 50);
        $this->dispatchModerationNotification(
            $post->user,
            'Post Restored',
            "Great news! After a thorough review, we have confirmed that your post from {$post->created_at->format('M d, Y')} containing \"{$contentSnippet}\" complies with our community guidelines. It is now fully visible again on the feed. Thank you for your patience during this process.",
            'success',
            "/agripulse/post/{$post->id}",
            $post->image_url ?? null,
            [
                'post_content' => $post->content,
                'post_date' => $post->created_at->toIso8601String(),
                'restored_at' => now()->toIso8601String(),
                'metrics' => [
                    'reactions' => $post->reactions()->count(),
                    'comments' => $post->comments()->count(),
                    'reports' => $post->reports()->count(),
                ],
                'author' => [
                    'name' => $post->user->name,
                    'total_posts' => $post->user->posts()->count(),
                    'joined_at' => $post->user->created_at->toIso8601String(),
                ]
            ]
        );

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

        // Notify Reporter
        $subject = $report->post ? \Illuminate\Support\Str::limit($report->post->content, 30) : 'content';
        $this->dispatchModerationNotification(
            $report->reporter,
            'Report Resolved',
            "We have completed our review of the report you submitted on {$report->created_at->format('M d, Y')} regarding \"{$subject}\". We have taken the appropriate enforcement actions in accordance with our policies. Thank you for being a vigilant member of our community and helping keep AgriPulse safe for everyone.",
            'success',
            $report->post_id ? "/agripulse/post/{$report->post_id}" : null,
            $report->post?->image_url ?? null,
            [
                'post_content' => $report->post?->content,
                'post_date' => $report->post?->created_at?->toIso8601String(),
                'resolved_at' => now()->toIso8601String(),
                'metrics' => $report->post ? [
                    'reactions' => $report->post->reactions()->count(),
                    'comments' => $report->post->comments()->count(),
                    'reports' => $report->post->reports()->count(),
                ] : null,
                'reporter' => [
                    'name' => $report->reporter->name,
                    'joined_at' => $report->reporter->created_at->toIso8601String(),
                ]
            ]
        );

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

        // Notify Author
        $contentSnippet = \Illuminate\Support\Str::limit($comment->content, 50);
        $parentPost = $comment->post; // Assuming relationship
        $parentSnippet = $parentPost ? \Illuminate\Support\Str::limit($parentPost->content, 50) : 'Unknown Post';
        
        $this->dispatchModerationNotification(
            $comment->user,
            'Comment Removed',
            "Your comment \"{$contentSnippet}\" posted on a discussion has been removed because it violated our community guidelines. We ask that you ensure all future contributions remain respectful and relevant to the conversation.",
            'critical',
            null,
            null,
            [
                'comment_content' => $comment->content,
                'parent_post_content' => $parentPost->content ?? '',
                'parent_post_author' => $parentPost->user->name ?? 'Unknown',
                'parent_post_snippet' => $parentSnippet,
                'removed_at' => now()->toIso8601String()
            ]
        );

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

        // Notify Author
        $contentSnippet = \Illuminate\Support\Str::limit($comment->content, 50);
        $parentPost = $comment->post;
        $parentSnippet = $parentPost ? \Illuminate\Support\Str::limit($parentPost->content, 50) : 'Unknown Post';

        $this->dispatchModerationNotification(
            $comment->user,
            'Comment Hidden',
            "Your comment \"{$contentSnippet}\" has been temporarily hidden while our moderation team reviews it for potential policy violations. You will be notified if any further action is required.",
            'high',
            null,
            null,
            [
                'comment_content' => $comment->content,
                'parent_post_content' => $parentPost->content ?? '',
                'parent_post_author' => $parentPost->user->name ?? 'Unknown',
                'parent_post_snippet' => $parentSnippet,
                'hidden_at' => now()->toIso8601String()
            ]
        );

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

        // Notify Author
        $contentSnippet = \Illuminate\Support\Str::limit($comment->content, 50);
        $parentPost = $comment->post;
        $parentSnippet = $parentPost ? \Illuminate\Support\Str::limit($parentPost->content, 50) : 'Unknown Post';

        $this->dispatchModerationNotification(
            $comment->user,
            'Comment Restored',
            "Good news! Your comment \"{$contentSnippet}\" has been restored after review. It is now visible to the community again. Thank you for your patience.",
            'success',
            null,
            null,
            [
                'comment_content' => $comment->content,
                'parent_post_content' => $parentPost->content ?? '',
                'parent_post_author' => $parentPost->user->name ?? 'Unknown',
                'parent_post_snippet' => $parentSnippet,
                'restored_at' => now()->toIso8601String()
            ]
        );

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

        // Notify User
        $this->dispatchModerationNotification(
            $user,
            'Account Warning',
            "You have received a formal warning: {$request->reason}. Please review our community guidelines to avoid further action.",
            'critical'
        );

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

        // Notify User
        $this->dispatchModerationNotification(
            $user,
            'AgriPulse Access Suspended',
            "Your access to AgriPulse has been suspended. Reason: {$request->reason}.",
            'critical'
        );

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

        // Notify User
        $this->dispatchModerationNotification(
            $user,
            'Access Restored',
            'Your access to AgriPulse has been fully restored. Welcome back!',
            'success'
        );

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
                            
                            // Notify Author
                            $contentSnippet = \Illuminate\Support\Str::limit($post->content, 50);
                            $this->dispatchModerationNotification(
                                $post->user,
                                'Your Post Was Hidden',
                                "We wanted to let you know that your post from {$post->created_at->format('M d, Y')} containing \"{$contentSnippet}\" has been temporarily hidden. This action was taken to allow our moderation team to review it against our community guidelines regarding safe and appropriate content. You will be notified once a final decision is made.",
                                'high',
                                "/agripulse/post/{$post->id}",
                                $post->image_url ?? null,
                                [
                                    'post_content' => $post->content,
                                    'post_date' => $post->created_at->toIso8601String(),
                                    'hidden_at' => now()->toIso8601String(),
                                    'metrics' => [
                                        'reactions' => $post->reactions()->count(),
                                        'comments' => $post->comments()->count(),
                                        'reports' => $post->reports()->count(),
                                    ],
                                    'author' => [
                                        'name' => $post->user->name,
                                        'total_posts' => $post->user->posts()->count(),
                                        'joined_at' => $post->user->created_at->toIso8601String(),
                                    ]
                                ]
                            );
                        }
                        if ($action === 'delete') {
                            $post->delete();
                            $this->logModerationAction('delete_post', $post, ['bulk' => true, 'reason' => $reason]);

                            // Notify Author
                            $contentSnippet = \Illuminate\Support\Str::limit($post->content, 50);
                            $this->dispatchModerationNotification(
                                $post->user,
                                'Your Post Was Removed',
                                "We regret to inform you that your post from {$post->created_at->format('M d, Y')} containing \"{$contentSnippet}\" has been permanently removed from AgriPulse. This decision was made because the content was found to be in violation of our Terms of Service or Community Standards. Please review our guidelines to avoid future infractions.",
                                'critical',
                                "/agripulse/post/{$post->id}",
                                $post->image_url ?? null,
                                [
                                    'post_content' => $post->content,
                                    'post_date' => $post->created_at->toIso8601String(),
                                    'removed_at' => now()->toIso8601String(),
                                    'metrics' => [
                                        'reactions' => $post->reactions()->count(),
                                        'comments' => $post->comments()->count(),
                                        'reports' => $post->reports()->count(),
                                    ],
                                    'author' => [
                                        'name' => $post->user->name,
                                        'total_posts' => $post->user->posts()->count(),
                                        'joined_at' => $post->user->created_at->toIso8601String(),
                                    ]
                                ]
                            );
                        }
                        if ($action === 'restore') {
                            if ($post->trashed()) $post->restore();
                            $post->update(['status' => 'published']);
                            $this->logModerationAction('restore_post', $post, ['bulk' => true, 'reason' => $reason]);

                            // Notify Author
                            $contentSnippet = \Illuminate\Support\Str::limit($post->content, 50);
                            $this->dispatchModerationNotification(
                                $post->user,
                                'Post Restored',
                                "Great news! After a thorough review, we have confirmed that your post from {$post->created_at->format('M d, Y')} containing \"{$contentSnippet}\" complies with our community guidelines. It is now fully visible again on the feed. Thank you for your patience during this process.",
                                'success',
                                "/agripulse/post/{$post->id}",
                                $post->image_url ?? null,
                                [
                                    'post_content' => $post->content,
                                    'post_date' => $post->created_at->toIso8601String(),
                                    'restored_at' => now()->toIso8601String(),
                                    'metrics' => [
                                        'reactions' => $post->reactions()->count(),
                                        'comments' => $post->comments()->count(),
                                        'reports' => $post->reports()->count(),
                                    ],
                                    'author' => [
                                        'name' => $post->user->name,
                                        'total_posts' => $post->user->posts()->count(),
                                        'joined_at' => $post->user->created_at->toIso8601String(),
                                    ]
                                ]
                            );
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

                            // Notify Author
                            $contentSnippet = \Illuminate\Support\Str::limit($comment->content, 50);
                            $parentPost = $comment->post;
                            $parentSnippet = $parentPost ? \Illuminate\Support\Str::limit($parentPost->content, 50) : 'Unknown Post';

                            $this->dispatchModerationNotification(
                                $comment->user,
                                'Comment Hidden',
                                "Your comment \"{$contentSnippet}\" has been temporarily hidden while our moderation team reviews it for potential policy violations. You will be notified if any further action is required.",
                                'high',
                                null,
                                null,
                                [
                                    'comment_content' => $comment->content,
                                    'parent_post_content' => $parentPost->content ?? '',
                                    'parent_post_author' => $parentPost->user->name ?? 'Unknown',
                                    'parent_post_snippet' => $parentSnippet,
                                    'hidden_at' => now()->toIso8601String()
                                ]
                            );
                        }
                        if ($action === 'delete') {
                            $comment->delete();
                            $this->logModerationAction('delete_comment', $comment, ['bulk' => true, 'reason' => $reason]);

                            // Notify Author
                            $contentSnippet = \Illuminate\Support\Str::limit($comment->content, 50);
                            $parentPost = $comment->post; // Assuming relationship
                            $parentSnippet = $parentPost ? \Illuminate\Support\Str::limit($parentPost->content, 50) : 'Unknown Post';
                            
                            $this->dispatchModerationNotification(
                                $comment->user,
                                'Comment Removed',
                                "Your comment \"{$contentSnippet}\" posted on a discussion has been removed because it violated our community guidelines. We ask that you ensure all future contributions remain respectful and relevant to the conversation.",
                                'critical',
                                null,
                                null,
                                [
                                    'comment_content' => $comment->content,
                                    'parent_post_content' => $parentPost->content ?? '',
                                    'parent_post_author' => $parentPost->user->name ?? 'Unknown',
                                    'parent_post_snippet' => $parentSnippet,
                                    'removed_at' => now()->toIso8601String()
                                ]
                            );
                        }
                        if ($action === 'restore') {
                            if ($comment->trashed()) $comment->restore();
                            $comment->update(['status' => 'published']);
                            $this->logModerationAction('restore_comment', $comment, ['bulk' => true, 'reason' => $reason]);

                            // Notify Author
                            $contentSnippet = \Illuminate\Support\Str::limit($comment->content, 50);
                            $parentPost = $comment->post;
                            $parentSnippet = $parentPost ? \Illuminate\Support\Str::limit($parentPost->content, 50) : 'Unknown Post';

                            $this->dispatchModerationNotification(
                                $comment->user,
                                'Comment Restored',
                                "Good news! Your comment \"{$contentSnippet}\" has been restored after review. It is now visible to the community again. Thank you for your patience.",
                                'success',
                                null,
                                null,
                                [
                                    'comment_content' => $comment->content,
                                    'parent_post_content' => $parentPost->content ?? '',
                                    'parent_post_author' => $parentPost->user->name ?? 'Unknown',
                                    'parent_post_snippet' => $parentSnippet,
                                    'restored_at' => now()->toIso8601String()
                                ]
                            );
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

                            // Notify Reporter
                            $subject = $report->post_id ? "Post #{$report->post_id}" : "User #{$report->reporter_id}";
                            $this->dispatchModerationNotification(
                                $report->reporter,
                                'Report Resolved',
                                "We have completed our review of the report you submitted on {$report->created_at->format('M d, Y')} regarding \"{$subject}\". We have taken the appropriate enforcement actions in accordance with our policies. Thank you for being a vigilant member of our community and helping keep AgriPulse safe for everyone.",
                                'success',
                                $report->post_id ? "/agripulse/post/{$report->post_id}" : null,
                                $report->post?->image_url ?? null,
                                [
                                    'post_content' => $report->post?->content,
                                    'post_date' => $report->post?->created_at?->toIso8601String(),
                                    'resolved_at' => now()->toIso8601String(),
                                    'metrics' => $report->post ? [
                                        'reactions' => $report->post->reactions()->count(),
                                        'comments' => $report->post->comments()->count(),
                                        'reports' => $report->post->reports()->count(),
                                    ] : null,
                                    'reporter' => [
                                        'name' => $report->reporter->name,
                                        'joined_at' => $report->reporter->created_at->toIso8601String(),
                                    ]
                                ]
                            );
                        }
                        if ($action === 'dismiss') {
                            $report->update([
                                'status' => 'dismissed',
                                'resolved_by' => auth()->id(),
                                'resolved_at' => now(),
                                'resolution_note' => $note ?? 'Bulk dismissal'
                            ]);
                            $this->logModerationAction('dismiss_report', $report, ['bulk' => true, 'note' => $note ?? 'Bulk dismissal']);

                            // Notify Reporter
                            $subject = $report->post_id ? "Post #{$report->post_id}" : "User #{$report->reporter_id}";
                            $this->dispatchModerationNotification(
                                $report->reporter,
                                'Report Update',
                                "Thank you for reporting \"{$subject}\". After a careful review, we have determined that this content does not violate our community guidelines at this time. However, your feedback is invaluable in keeping our platform safe.",
                                'normal'
                            );
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

                            // Notify User
                            $this->dispatchModerationNotification(
                                $user,
                                'Account Verified',
                                "Your account has been successfully verified. You now have full access to all AgriPulse features. Welcome to our community!",
                                'success'
                            );
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

                            // Notify User
                            $this->dispatchModerationNotification(
                                $user,
                                'AgriPulse Access Suspended',
                                "Your access to AgriPulse has been suspended. Reason: " . ($reason ?? 'Compliance review') . ".",
                                'critical'
                            );
                        }
                        if ($action === 'activate') {
                            $user->update(['agripulse_banned_at' => null]);
                            $this->logModerationAction('activate_user', $user, ['bulk' => true]);

                             // Notify User
                            $this->dispatchModerationNotification(
                                $user,
                                'Access Restored',
                                'Your access to AgriPulse has been fully restored. Welcome back!',
                                'success'
                            );
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

    /**
     * Dispatch a moderation notification job.
     */
    private function dispatchModerationNotification($user, $subject, $message, $priority = 'normal', $url = null, $imageUrl = null, $contentDetails = [])
    {
        if (!$user) return;
        
        SendModerationNotification::dispatch($user, [
            'subject' => $subject,
            'message' => $message,
            'priority' => $priority,
            'action_url' => $url,
            'image_url' => $imageUrl,
            'content_details' => $contentDetails
        ]);
    }

    /**
     * Export posts (all or selected).
     */
    public function exportPosts(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel,word,csv',
            'ids' => 'nullable|array',
            'filters' => 'nullable|array'
        ]);

        \App\Jobs\AgriPulse\ExportModerationData::dispatch(
            auth()->user(),
            'posts',
            $request->format,
            $request->ids,
            $request->filters ?? []
        );

        return response()->json([
            'message' => 'Export started. You will be notified when ready for download.'
        ]);
    }

    /**
     * Export comments (all or selected).
     */
    public function exportComments(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel,word,csv',
            'ids' => 'nullable|array',
            'filters' => 'nullable|array'
        ]);

        \App\Jobs\AgriPulse\ExportModerationData::dispatch(
            auth()->user(),
            'comments',
            $request->format,
            $request->ids,
            $request->filters ?? []
        );

        return response()->json([
            'message' => 'Export started. You will be notified when ready for download.'
        ]);
    }

    /**
     * Export reports (all or selected).
     */
    public function exportReports(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel,word,csv',
            'ids' => 'nullable|array',
            'filters' => 'nullable|array'
        ]);

        \App\Jobs\AgriPulse\ExportModerationData::dispatch(
            auth()->user(),
            'reports',
            $request->format,
            $request->ids,
            $request->filters ?? []
        );

        return response()->json([
            'message' => 'Export started. You will be notified when ready for download.'
        ]);
    }

    /**
     * Export users (all or selected).
     */
    public function exportUsers(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel,word,csv',
            'ids' => 'nullable|array',
            'filters' => 'nullable|array'
        ]);

        \App\Jobs\AgriPulse\ExportModerationData::dispatch(
            auth()->user(),
            'users',
            $request->format,
            $request->ids,
            $request->filters ?? []
        );

        return response()->json([
            'message' => 'Export started. You will be notified when ready for download.'
        ]);
    }

    /**
     * Export activity logs (all or selected).
     */
    public function exportLogs(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel,word,csv',
            'ids' => 'nullable|array',
            'filters' => 'nullable|array'
        ]);

        \App\Jobs\AgriPulse\ExportModerationData::dispatch(
            auth()->user(),
            'logs',
            $request->format,
            $request->ids,
            $request->filters ?? []
        );

        return response()->json([
            'message' => 'Export started. You will be notified when ready for download.'
        ]);
    }

    /**
     * Download exported file (signed route).
     */
    public function downloadExport(Request $request, string $filename)
    {
        $filepath = storage_path("app/private/exports/agripulse/{$filename}");

        if (!file_exists($filepath)) {
            abort(404, 'Export file not found or has expired.');
        }

        return response()->download($filepath)->deleteFileAfterSend(false);
    }
}
