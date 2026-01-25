<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'status',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user that created the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (for nested replies).
     */
    public function parent()
    {
        return $this->belongsTo(PostComment::class, 'parent_id');
    }

    /**
     * Get all replies to this comment (recursive).
     */
    public function replies()
    {
        return $this->hasMany(PostComment::class, 'parent_id')
            ->with(['user', 'replies']); // Load nested replies recursively
    }

    /**
     * Scope a query to only include published comments.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orWhereNull('status');
    }
}
