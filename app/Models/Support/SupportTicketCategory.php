<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'icon',
        'color',
        'is_active',
        'auto_assign_to_department_id',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SupportTicketCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SupportTicketCategory::class, 'parent_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(SupportDepartment::class, 'auto_assign_to_department_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'category_id');
    }
}
