<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportBusinessHours extends Model
{
    protected $fillable = [
        'name',
        'timezone',
        'monday_start',
        'monday_end',
        'tuesday_start',
        'tuesday_end',
        'wednesday_start',
        'wednesday_end',
        'thursday_start',
        'thursday_end',
        'friday_start',
        'friday_end',
        'saturday_start',
        'saturday_end',
        'sunday_start',
        'sunday_end',
    ];

    public function holidays(): HasMany
    {
        return $this->hasMany(SupportHoliday::class, 'business_hours_id');
    }

    public function slaPolicies(): HasMany
    {
        return $this->hasMany(SupportSlaPolicy::class, 'business_hours_id');
    }
}
