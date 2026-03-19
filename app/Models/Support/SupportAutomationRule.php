<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;

class SupportAutomationRule extends Model
{
    protected $fillable = [
        'name',
        'description',
        'trigger_type',
        'conditions',
        'actions',
        'priority',
        'is_active',
        'run_count',
        'last_run_at'
    ];

    protected $casts = [
        'conditions' => 'json',
        'actions' => 'json',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime'
    ];
}
