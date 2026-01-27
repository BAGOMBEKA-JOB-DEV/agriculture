<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $with = ['roles.permissions'];

    protected $appends = ['permissions_list', 'avatar_url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'role',
        'location',
        'phone',
        'status',
        'email_verified_at',
        'notification_preferences',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'two_factor_type',
        'agripulse_banned_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'notification_preferences' => 'array',
            'two_factor_confirmed_at' => 'datetime',
            'agripulse_banned_at' => 'datetime',
        ];
    }

    // Agriculture Marketplace Relationships

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function buyerProfile(): HasOne
    {
        return $this->hasOne(BuyerProfile::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class, $this->role === 'vendor' ? 'seller_id' : 'buyer_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->first();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function postReactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function postComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function savedPosts(): HasMany
    {
        return $this->hasMany(SavedPost::class);
    }

    public function profileViewsGiven(): HasMany
    {
        return $this->hasMany(ProfileView::class, 'viewer_id');
    }

    public function profileViewsReceived(): HasMany
    {
        return $this->hasMany(ProfileView::class, 'viewed_user_id');
    }

    public function moderationActions(): HasMany
    {
        return $this->hasMany(AgriPulseUserModerationAction::class);
    }

    public function performedModerationLogs(): HasMany
    {
        return $this->hasMany(AgriPulseModerationLog::class, 'moderator_id');
    }

    public function receivedReports(): HasManyThrough
    {
        return $this->hasManyThrough(AgriPulsePostReport::class, Post::class);
    }

    public function getPermissionsListAttribute()
    {
        return $this->roles->flatMap->permissions->pluck('slug')->unique()->values();
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Return a UI Avatar placeholder if no avatar set
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF";
    }
}
