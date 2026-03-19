<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportDepartment extends Model
{
    protected $fillable = [
        'name',
        'description',
        'email',
        'parent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SupportDepartment::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SupportDepartment::class, 'parent_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'department_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(SupportTicketCategory::class, 'auto_assign_to_department_id');
    }
}
