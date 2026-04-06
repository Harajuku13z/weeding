<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestCompanion extends Model
{
    protected $fillable = ['guest_id', 'first_name', 'last_name', 'dietary_restrictions'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
