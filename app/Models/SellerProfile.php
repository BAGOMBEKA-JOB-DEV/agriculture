<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'products_sold',
        'business_description',
        'business_registration',
        'certifications',
        'documents',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'certifications' => 'array',
        'documents' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
