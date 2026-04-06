<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorPaletteItem extends Model
{
    protected $fillable = ['wedding_id', 'name', 'hex_color', 'description', 'sort_order'];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }
}
