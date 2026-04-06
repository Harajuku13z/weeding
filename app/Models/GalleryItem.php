<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    protected $fillable = ['wedding_id', 'image', 'video', 'caption', 'description', 'sort_order'];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function isVideo(): bool
    {
        return !empty($this->video);
    }

    public function getMediaUrl(): ?string
    {
        return $this->video
            ? Storage::url($this->video)
            : ($this->image ? Storage::url($this->image) : null);
    }
}
