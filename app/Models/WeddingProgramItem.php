<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeddingProgramItem extends Model
{
    protected $fillable = [
        'wedding_id', 'title', 'date', 'time', 'venue_name', 'address',
        'description', 'icon', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
        'is_published' => 'boolean',
    ];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function getDateTimeFormatted(): string
    {
        if (!$this->date) return 'Date à venir';
        $date = $this->date->translatedFormat('d F Y');
        $time = $this->time ? ' à ' . substr($this->time, 0, 5) : '';
        return $date . $time;
    }
}
