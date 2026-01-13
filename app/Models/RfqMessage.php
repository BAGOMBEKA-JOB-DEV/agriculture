<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
    ];

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function attachments()
    {
        return $this->hasMany(RfqMessageAttachment::class, 'rfq_message_id');
    }
}
