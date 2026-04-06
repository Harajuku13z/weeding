<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'wedding_id', 'guest_id', 'message_template_id', 'channel', 'status',
        'message_content', 'recipient_email', 'recipient_phone',
        'sent_at', 'scheduled_at', 'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'message_template_id');
    }
}
