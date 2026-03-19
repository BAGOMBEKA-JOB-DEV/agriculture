<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SupportTicketTag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(SupportTicket::class, 'support_ticket_tag_pivot', 'tag_id', 'ticket_id');
    }
}
