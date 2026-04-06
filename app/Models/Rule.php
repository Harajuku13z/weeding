<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = ['wedding_id', 'type', 'icon', 'title', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }
}
