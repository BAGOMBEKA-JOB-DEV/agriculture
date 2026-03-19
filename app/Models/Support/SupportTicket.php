<?php

namespace App\Models\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SupportTicket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'customer_id',
        'assigned_agent_id',
        'department_id',
        'category_id',
        'priority',
        'status',
        'source',
        'brand_id',
        'parent_ticket_id',
        'merged_into_ticket_id',
        'sla_policy_id',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'due_date',
        'tags',
        'custom_fields',
        'is_spam',
        'spam_score',
    ];

    protected $casts = [
        'tags' => 'json',
        'custom_fields' => 'json',
        'is_spam' => 'boolean',
        'spam_score' => 'decimal:2',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(SupportDepartment::class, 'department_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SupportTicketCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(SupportBrand::class, 'brand_id');
    }

    public function slaPolicy(): BelongsTo
    {
        return $this->belongsTo(SupportSlaPolicy::class, 'sla_policy_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'ticket_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(SupportTicketHistory::class, 'ticket_id');
    }

    public function watchers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'support_ticket_watchers', 'ticket_id', 'user_id')
            ->withTimestamps();
    }

    public function tags_pivot(): BelongsToMany
    {
        return $this->belongsToMany(SupportTicketTag::class, 'support_ticket_tag_pivot', 'ticket_id', 'tag_id')
            ->withTimestamps();
    }

    public function parentTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'parent_ticket_id');
    }

    public function subTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'parent_ticket_id');
    }
}
