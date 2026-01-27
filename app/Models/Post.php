<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'content',
        'image_url',
        'type',
        'original_post_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function reposts(): HasMany
    {
        return $this->hasMany(Post::class, 'original_post_id');
    }

    public function originalPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'original_post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function savedPosts(): HasMany
    {
        return $this->hasMany(SavedPost::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AgriPulsePostReport::class, 'post_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeHidden($query)
    {
        return $query->where('status', 'hidden');
    }

    public function scopeFlagged($query)
    {
        return $query->where('status', 'flagged');
    }
}
