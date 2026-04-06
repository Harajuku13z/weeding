<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeddingVenue extends Model
{
    protected $fillable = [
        'wedding_id', 'name', 'address', 'city', 'google_maps_url', 'waze_url',
        'description', 'photo', 'type', 'sort_order',
    ];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }
}
