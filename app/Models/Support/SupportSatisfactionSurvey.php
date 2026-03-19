<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportSatisfactionSurvey extends Model
{
    protected $fillable = [
        'ticket_id',
        'rating',
        'nps_score',
        'feedback_text',
        'survey_type',
        'sent_at',
        'responded_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }
}
