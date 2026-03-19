<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportBrand extends Model
{
    protected $fillable = [
        'name',
        'subdomain',
        'logo_url',
        'primary_color',
        'support_email',
        'support_phone',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'json',
        'is_active' => 'boolean',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'brand_id');
    }
}
