<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReaction;
use App\Models\PostComment;
use App\Models\SavedPost;
use App\Models\PostView;
use App\Models\ProfileView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AgriPulseController extends Controller
{
    /**
     * Display the AgriPulse feed.
     */
    public function index(Request $request)
    {
        $posts = Post::with([
            'user',
            'product',
            'originalPost.user',
            'originalPost.product'
        ])
        ->withCount(['reactions', 'reposts', 'comments', 'views'])
        ->latest()
        ->paginate(15);

        // Check if current user has reacted to each post and if they saved it
        $user = auth()->user();
        if ($user) {
            $posts->each(function ($post) use ($user) {
                $post->user_reaction = $post->reactions()
                    ->where('user_id', $user->id)
                    ->first();
                
                $post->is_saved = SavedPost::where('user_id', $user->id)
                    ->where('post_id', $post->id)
                    ->exists();
            });
        }

        return response()->json($posts);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required_without:image|string|nullable',
            'product_id' => 'nullable|exists:products,id',
            'image' => 'nullable|image|max:5120', // 5MB max
            'type' => 'nullable|in:post,repost',
            'original_post_id' => 'nullable|required_if:type,repost|exists:posts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'product_id' => $request->product_id,
            'image_url' => $imageUrl,
            'type' => $request->type ?: 'post',
            'original_post_id' => $request->original_post_id,
        ]);

        return response()->json($post->load(['user', 'product', 'originalPost.user', 'originalPost.product']), 201);
    }

    /**
     * Add/Update/Remove a reaction to a post.
     */
    public function react(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:like,love,celebrate,funny,insightful,support',
        ]);

        $post = Post::findOrFail($id);
        $userId = auth()->id();

        $existingReaction = PostReaction::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->type === $request->type) {
                // Remove reaction if same type (toggle off)
                $existingReaction->delete();
                return response()->json(['message' => 'Reaction removed', 'reaction' => null]);
            } else {
                // Update reaction type
                $existingReaction->update(['type' => $request->type]);
                return response()->json(['message' => 'Reaction updated', 'reaction' => $existingReaction]);
            }
        }

        $reaction = PostReaction::create([
            'post_id' => $post->id,
            'user_id' => $userId,
            'type' => $request->type,
        ]);

        return response()->json(['message' => 'Reaction added', 'reaction' => $reaction], 201);
    }

    /**
     * Remove the specified post.
     */
    public function destroy($id)
    {
        $post = Post::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Delete image if exists
        if ($post->image_url) {
            $path = str_replace(asset('storage/'), '', $post->image_url);
            Storage::disk('public')->delete($path);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    /**
     * Show a single post with full details.
     */
    public function showPost($postId)
    {
        $post = Post::with([
            'user',
            'product',
            'originalPost.user',
            'originalPost.product'
        ])
        ->withCount(['reactions', 'reposts', 'comments', 'views'])
        ->findOrFail($postId);

        // Get current user's reaction and saved status
        $user = auth()->user();
        if ($user) {
            $post->user_reaction = $post->reactions()
                ->where('user_id', $user->id)
                ->first();
            
            $post->is_saved = SavedPost::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->exists();
        }

        // Get reaction breakdown
        $post->reaction_breakdown = PostReaction::where('post_id', $postId)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        // Get all comments with nested replies (recursive)
        $post->all_comments = PostComment::where('post_id', $postId)
            ->whereNull('parent_id')
            ->with(['user', 'replies']) // replies will auto-load recursively via model
            ->latest()
            ->get();

        return response()->json($post);
    }

    /**
     * Get comments for a post.
     */
    public function getComments(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        
        $comments = PostComment::where('post_id', $postId)
            ->whereNull('parent_id')
            ->with(['user', 'replies']) // Load recursively via model
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }

    /**
     * Store a comment on a post.
     */
    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|min:1|max:1000',
            'parent_id' => 'nullable|exists:post_comments,id',
        ]);

        $post = Post::findOrFail($postId);

        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    /**
     * Delete a comment.
     */
    public function destroyComment($commentId)
    {
        $comment = PostComment::where('id', $commentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    /**
     * Toggle save/unsave a post.
     */
    public function savePost(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $userId = auth()->id();

        $savedPost = SavedPost::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->first();

        if ($savedPost) {
            $savedPost->delete();
            return response()->json(['message' => 'Post unsaved', 'is_saved' => false]);
        }

        SavedPost::create([
            'user_id' => $userId,
            'post_id' => $post->id,
        ]);

        return response()->json(['message' => 'Post saved', 'is_saved' => true], 201);
    }

    /**
     * Get saved posts for the authenticated user.
     */
    public function getSavedPosts(Request $request)
    {
        $savedPosts = SavedPost::where('user_id', auth()->id())
            ->with(['post.user', 'post.product', 'post.originalPost.user'])
            ->latest()
            ->paginate(15);

        $posts = $savedPosts->map(function ($savedPost) {
            $post = $savedPost->post;
            $post->is_saved = true;
            
            // Add reaction check
            $post->user_reaction = $post->reactions()
                ->where('user_id', auth()->id())
                ->first();
                
            return $post;
        });

        return response()->json([
            'data' => $posts,
            'current_page' => $savedPosts->currentPage(),
            'last_page' => $savedPosts->lastPage(),
            'next_page_url' => $savedPosts->nextPageUrl(),
        ]);
    }

    /**
     * Track a post view.
     */
    public function trackPostView(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $userId = auth()->id();
        $ipAddress = $request->ip();

        // Only track once per user per post
        if ($userId) {
            PostView::firstOrCreate([
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);
        } else {
            // Track by IP for guests
            PostView::firstOrCreate([
                'post_id' => $post->id,
                'ip_address' => $ipAddress,
            ], [
                'user_id' => null,
            ]);
        }

        return response()->json(['message' => 'View tracked']);
    }

    /**
     * Track a profile view.
     */
    public function trackProfileView(Request $request, $userId)
    {
        $viewerId = auth()->id();
        
        // Don't track self-views
        if ($viewerId == $userId) {
            return response()->json(['message' => 'Self-view not tracked']);
        }

        ProfileView::create([
            'viewer_id' => $viewerId,
            'viewed_user_id' => $userId,
        ]);

        return response()->json(['message' => 'Profile view tracked'], 201);
    }

    /**
     * Get current user's AgriPulse stats (for sidebar).
     */
    public function getMyStats(Request $request)
    {
        $userId = auth()->id();

        // Total post views (sum of all views on user's posts)
        $postViews = PostView::whereHas('post', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        // Total profile views received
        $profileViews = ProfileView::where('viewed_user_id', $userId)->count();

        // Saved posts count
        $savedPosts = SavedPost::where('user_id', $userId)->count();

        return response()->json([
            'post_views' => $postViews,
            'profile_views' => $profileViews,
            'saved_posts_count' => $savedPosts,
        ]);
    }

    /**
     * Get latest posts from super admins (for Market Pulse News).
     */
    public function getSuperAdminPosts(Request $request)
    {
        $superAdminPosts = Post::whereHas('user.roles', function ($query) {
            $query->where('slug', 'super_admin');
        })
        ->with(['user', 'reactions', 'comments'])
        ->withCount(['reactions', 'comments', 'views'])
        ->latest()
        ->limit(5)
        ->get();

        return response()->json($superAdminPosts);
    }

    /**
     * Get user profile data.
     */
    public function getUserProfile(Request $request, $userId)
    {
        $user = \App\Models\User::with('roles')->findOrFail($userId);

        // Get user's posts
        $posts = Post::where('user_id', $userId)
            ->with(['user', 'product', 'originalPost.user'])
            ->withCount(['reactions', 'reposts', 'comments', 'views'])
            ->latest()
            ->paginate(10);

        // Check if current user has reacted to each post and if they saved it
        $currentUser = auth()->user();
        if ($currentUser) {
            $posts->each(function ($post) use ($currentUser) {
                $post->user_reaction = $post->reactions()
                    ->where('user_id', $currentUser->id)
                    ->first();
                
                $post->is_saved = SavedPost::where('user_id', $currentUser->id)
                    ->where('post_id', $post->id)
                    ->exists();
            });
        }

        // Get stats
        $postViews = PostView::whereHas('post', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $profileViews = ProfileView::where('viewed_user_id', $userId)->count();
        $totalPosts = Post::where('user_id', $userId)->count();
        $totalReactions = PostReaction::whereHas('post', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        return response()->json([
            'user' => $user,
            'posts' => $posts,
            'stats' => [
                'post_views' => $postViews,
                'profile_views' => $profileViews,
                'total_posts' => $totalPosts,
                'total_reactions' => $totalReactions,
            ],
        ]);
    }

    /**
     * Get suggested users (for profile sidebar).
     */
    public function getSuggestedUsers(Request $request)
    {
        $currentUserId = auth()->id();
        
        // Get users with similar roles or active in AgriPulse
        $suggestedUsers = \App\Models\User::with('roles')
            ->where('id', '!=', $currentUserId)
            ->whereHas('posts') // Users who have posted
            ->withCount(['posts', 'profileViewsReceived'])
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->role,
                    'posts_count' => $user->posts_count,
                    'profile_views_count' => $user->profile_views_received_count,
                ];
            });

        return response()->json($suggestedUsers);
    }
}
