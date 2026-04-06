<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_id', 'first_name', 'last_name', 'email', 'phone',
        'invitation_code', 'rsvp_status', 'companions_count', 'companions_allowed',
        'max_companions', 'dietary_restrictions', 'notes_admin', 'tags',
        'contact_channel', 'is_suspended', 'personal_message', 'rsvp_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'companions_allowed' => 'boolean',
        'is_suspended' => 'boolean',
        'rsvp_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($guest) {
            if (!$guest->invitation_code) {
                $guest->invitation_code = strtoupper(Str::random(8));
            }
        });
    }

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function responses()
    {
        return $this->hasMany(GuestResponse::class);
    }

    public function companions()
    {
        return $this->hasMany(GuestCompanion::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function invitationTokens()
    {
        return $this->hasMany(InvitationToken::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->rsvp_status) {
            'accepted' => 'Accepté',
            'declined' => 'Décliné',
            'maybe' => 'À confirmer',
            default => 'En attente',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->rsvp_status) {
            'accepted' => 'success',
            'declined' => 'danger',
            'maybe' => 'warning',
            default => 'secondary',
        };
    }

    public function needsReminder(): bool
    {
        return in_array($this->rsvp_status, ['pending', 'maybe']) && !$this->is_suspended;
    }

    public function generateMagicToken(): InvitationToken
    {
        return $this->invitationTokens()->create([
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);
    }
}
