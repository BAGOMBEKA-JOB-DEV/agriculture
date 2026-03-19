<?php

namespace App\Models\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportCannedResponse extends Model
{
    protected $fillable = [
        'title',
        'shortcut',
        'content',
        'category',
        'is_public',
        'created_by',
        'department_id',
        'usage_count'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(SupportDepartment::class, 'department_id');
    }
}
