<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeddingSection extends Model
{
    protected $fillable = ['wedding_id', 'key', 'title', 'content', 'is_active', 'sort_order', 'extra'];

    protected $casts = [
        'is_active' => 'boolean',
        'extra' => 'array',
    ];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }
}
