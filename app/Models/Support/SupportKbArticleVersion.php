<?php

namespace App\Models\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportKbArticleVersion extends Model
{
    protected $fillable = ['article_id', 'title', 'content', 'version_number', 'created_by', 'change_note'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(SupportKbArticle::class, 'article_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
