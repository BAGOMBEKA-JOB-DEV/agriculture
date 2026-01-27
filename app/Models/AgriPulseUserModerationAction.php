<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgriPulseUserModerationAction extends Model
{
    protected $table = 'agripulse_user_moderation_actions';
    protected $fillable = [
        'user_id',
        'moderator_id',
        'action_type',
        'reason',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }
}
