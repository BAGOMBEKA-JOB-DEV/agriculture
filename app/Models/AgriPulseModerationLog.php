<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AgriPulseModerationLog extends Model
{
    protected $table = 'agripulse_moderation_logs';
    protected $fillable = [
        'moderator_id',
        'action',
        'subject_type',
        'subject_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }
}
