<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftItem extends Model
{
    protected $fillable = [
        'wedding_id', 'gift_category_id', 'name', 'description', 'external_link',
        'price', 'image', 'is_reserved', 'free_contribution', 'instructions',
        'reserved_message', 'sort_order',
    ];

    protected $casts = [
        'is_reserved' => 'boolean',
        'free_contribution' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function category()
    {
        return $this->belongsTo(GiftCategory::class, 'gift_category_id');
    }
}
