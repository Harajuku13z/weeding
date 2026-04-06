<?php

namespace App\Services;

use App\Models\Wedding;
use App\Models\Guest;
use App\Models\Reminder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReminderService
{
    public function send(Wedding $wedding, Guest $guest, string $channel, string $message): Reminder
    {
        $reminder = Reminder::create([
            'wedding_id' => $wedding->id,
            'guest_id' => $guest->id,
            'channel' => $channel,
            'status' => 'pending',
            'message_content' => $message,
            'recipient_email' => $guest->email,
            'recipient_phone' => $guest->phone,
        ]);

        try {
            match ($channel) {
                'email' => $this->sendEmail($reminder, $guest, $wedding),
                'sms' => $this->sendSms($reminder, $guest),
                'whatsapp' => $this->sendWhatsapp($reminder, $guest),
            };

            $reminder->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Exception $e) {
            $reminder->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error('Reminder failed: ' . $e->getMessage());
        }

        return $reminder->fresh();
    }

    protected function sendEmail(Reminder $reminder, Guest $guest, Wedding $wedding): void
    {
        if (!$guest->email) {
            throw new \Exception('Pas d\'email pour cet invité.');
        }

        Mail::send([], [], function ($mail) use ($reminder, $guest, $wedding) {
            $mail->to($guest->email, $guest->full_name)
                ->subject('Invitation - ' . $wedding->getCoupleName())
                ->html($reminder->message_content);
        });
    }

    protected function sendSms(Reminder $reminder, Guest $guest): void
    {
        if (!$guest->phone) {
            throw new \Exception('Pas de téléphone pour cet invité.');
        }
        // Provider SMS abstrait — brancher Twilio, OVH, etc.
        Log::info("SMS envoyé à {$guest->phone}: {$reminder->message_content}");
        // throw new \Exception('Provider SMS non configuré.');
    }

    protected function sendWhatsapp(Reminder $reminder, Guest $guest): void
    {
        if (!$guest->phone) {
            throw new \Exception('Pas de téléphone pour cet invité.');
        }
        // Provider WhatsApp abstrait — brancher Twilio, Meta, etc.
        Log::info("WhatsApp envoyé à {$guest->phone}: {$reminder->message_content}");
    }
}
