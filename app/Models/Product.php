<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'seller_id',
        'product_category_id',
        'unit',
        'min_order',
        'price',
        'location',
        'status',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class);
    }
}

