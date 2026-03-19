<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportSlaPolicy extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'priority_level',
        'customer_tier',
        'department_id',
        'response_time_minutes',
        'resolution_time_minutes',
        'business_hours_only',
        'business_hours_id',
        'pause_on_pending',
        'pause_on_customer_reply_wait',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'business_hours_only' => 'boolean',
        'pause_on_pending' => 'boolean',
        'pause_on_customer_reply_wait' => 'boolean',
        'response_time_minutes' => 'integer',
        'resolution_time_minutes' => 'integer',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(SupportDepartment::class, 'department_id');
    }

    public function businessHours(): BelongsTo
    {
        return $this->belongsTo(SupportBusinessHours::class, 'business_hours_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'sla_policy_id');
    }
}
