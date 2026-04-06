<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspirationItem extends Model
{
    protected $fillable = ['wedding_id', 'image', 'caption', 'category', 'sort_order'];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }
}
