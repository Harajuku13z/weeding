<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $fillable = ['wedding_id', 'name', 'channel', 'subject', 'body', 'type'];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function parseFor(Guest $guest, Wedding $wedding): string
    {
        return str_replace(
            ['{prenom}', '{nom}', '{nom_maries}', '{date_evenement}', '{lien_rsvp}', '{code_invitation}'],
            [
                $guest->first_name,
                $guest->last_name,
                $wedding->getCoupleName(),
                $wedding->getWeddingDateFormatted(),
                route('rsvp.show', ['wedding' => $wedding->slug, 'code' => $guest->invitation_code]),
                $guest->invitation_code,
            ],
            $this->body
        );
    }
}
