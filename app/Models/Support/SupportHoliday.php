<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportHoliday extends Model
{
    protected $fillable = [
        'name',
        'date',
        'business_hours_id',
        'is_recurring',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function businessHours(): BelongsTo
    {
        return $this->belongsTo(SupportBusinessHours::class, 'business_hours_id');
    }
}
