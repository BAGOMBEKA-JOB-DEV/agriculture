<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_name',
        'contact_person',
        'buyer_type',
        'business_description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
