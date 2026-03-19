<?php

namespace App\Models\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
        'message_type',
        'channel',
        'is_draft',
        'attachments',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_draft' => 'boolean',
        'attachments' => 'json',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function fileAttachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'message_id');
    }
}
