<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCategory extends Model
{
    protected $fillable = ['wedding_id', 'name', 'description', 'sort_order'];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function items()
    {
        return $this->hasMany(GiftItem::class)->orderBy('sort_order');
    }
}
