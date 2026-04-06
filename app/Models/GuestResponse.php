<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestResponse extends Model
{
    protected $fillable = ['guest_id', 'wedding_program_item_id', 'status', 'message'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function programItem()
    {
        return $this->belongsTo(WeddingProgramItem::class, 'wedding_program_item_id');
    }
}
