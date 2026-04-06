<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wedding extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bride_name', 'groom_name', 'slug', 'quote', 'intro_text',
        'welcome_message', 'wedding_date', 'logo', 'favicon', 'social_image',
        'seo_title', 'seo_description', 'is_published', 'is_draft',
        'envelope_animation', 'floral_decor', 'music_enabled', 'music_file',
        'rsvp_modification_allowed', 'rsvp_deadline', 'couple_photo',
        'hero_image', 'hero_video', 'story_text', 'accommodation_info',
        'accommodation_details', 'language',
    ];

    protected $casts = [
        'wedding_date' => 'date',
        'rsvp_deadline' => 'date',
        'is_published' => 'boolean',
        'is_draft' => 'boolean',
        'envelope_animation' => 'boolean',
        'floral_decor' => 'boolean',
        'music_enabled' => 'boolean',
        'rsvp_modification_allowed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function theme()
    {
        return $this->hasOne(WeddingTheme::class);
    }

    public function sections()
    {
        return $this->hasMany(WeddingSection::class)->orderBy('sort_order');
    }

    public function programItems()
    {
        return $this->hasMany(WeddingProgramItem::class)->orderBy('sort_order');
    }

    public function venues()
    {
        return $this->hasMany(WeddingVenue::class)->orderBy('sort_order');
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function galleryItems()
    {
        return $this->hasMany(GalleryItem::class)->orderBy('sort_order');
    }

    public function giftCategories()
    {
        return $this->hasMany(GiftCategory::class)->orderBy('sort_order');
    }

    public function giftItems()
    {
        return $this->hasMany(GiftItem::class)->orderBy('sort_order');
    }

    public function rules()
    {
        return $this->hasMany(Rule::class)->orderBy('sort_order');
    }

    public function colorPalette()
    {
        return $this->hasMany(ColorPaletteItem::class)->orderBy('sort_order');
    }

    public function inspirationItems()
    {
        return $this->hasMany(InspirationItem::class)->orderBy('sort_order');
    }

    public function messageTemplates()
    {
        return $this->hasMany(MessageTemplate::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function getCoupleName(): string
    {
        return $this->bride_name . ' & ' . $this->groom_name;
    }

    public function getWeddingDateFormatted(): string
    {
        return $this->wedding_date
            ? $this->wedding_date->translatedFormat('d F Y')
            : 'Date à venir';
    }

    public function getDaysUntilWedding(): ?int
    {
        if (!$this->wedding_date) return null;
        $now = now()->startOfDay();
        $weddingDate = $this->wedding_date->startOfDay();
        return $now->diffInDays($weddingDate, false);
    }

    // Statistiques RSVP
    public function getRsvpStats(): array
    {
        $guests = $this->guests;
        $total = $guests->count();
        $accepted = $guests->where('rsvp_status', 'accepted')->count();
        $declined = $guests->where('rsvp_status', 'declined')->count();
        $maybe = $guests->where('rsvp_status', 'maybe')->count();
        $pending = $guests->where('rsvp_status', 'pending')->count();
        $responded = $total - $pending;

        return [
            'total' => $total,
            'accepted' => $accepted,
            'declined' => $declined,
            'maybe' => $maybe,
            'pending' => $pending,
            'responded' => $responded,
            'response_rate' => $total > 0 ? round(($responded / $total) * 100) : 0,
            'expected_count' => $guests->where('rsvp_status', 'accepted')->sum(fn($g) => 1 + $g->companions_count),
        ];
    }
}
