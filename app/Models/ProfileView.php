<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileView extends Model
{
    use HasFactory;

    protected $fillable = [
        'viewer_id',
        'viewed_user_id',
    ];

    /**
     * Get the viewer (user who viewed the profile).
     */
    public function viewer()
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }

    /**
     * Get the viewed user (profile owner).
     */
    public function viewedUser()
    {
        return $this->belongsTo(User::class, 'viewed_user_id');
    }
}
