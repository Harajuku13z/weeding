<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeddingTheme extends Model
{
    protected $fillable = [
        'wedding_id', 'color_primary', 'color_secondary', 'color_accent',
        'color_background', 'color_text', 'font_title', 'font_body',
        'button_style', 'border_radius', 'shadow_intensity', 'animation_intensity',
        'dress_code_style', 'dress_code_formality', 'dress_code_description',
        'dress_code_men', 'dress_code_women', 'dress_code_accessories',
        'forbidden_colors', 'mood_description', 'mood_textures',
    ];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function getCssVariables(): string
    {
        return "
            --color-primary: {$this->color_primary};
            --color-secondary: {$this->color_secondary};
            --color-accent: {$this->color_accent};
            --color-background: {$this->color_background};
            --color-text: {$this->color_text};
            --border-radius: {$this->border_radius};
            --font-title: '{$this->font_title}', serif;
            --font-body: '{$this->font_body}', sans-serif;
        ";
    }
}
