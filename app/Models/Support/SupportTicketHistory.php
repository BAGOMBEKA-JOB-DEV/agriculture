<?php

namespace App\Models\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketHistory extends Model
{
    protected $table = 'support_ticket_history';
    public $timestamps = false; // Disable default timestamps (updated_at missing)

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'ticket_id',
        'user_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'description',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
