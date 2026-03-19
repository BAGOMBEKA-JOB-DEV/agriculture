<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportKbCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'icon', 'sort_order', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SupportKbCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SupportKbCategory::class, 'parent_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(SupportKbArticle::class, 'category_id');
    }
}
