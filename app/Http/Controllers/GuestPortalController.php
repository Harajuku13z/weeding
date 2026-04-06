<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\GuestCompanion;
use App\Models\GuestResponse;
use App\Models\InvitationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class GuestPortalController extends Controller
{
    /**
     * Page personnelle de l'invité via son code unique (/i/{code})
     * Pas de login — l'URL est le lien d'accès.
     */
    public function personalPage(string $code)
    {
        $guest = Guest::where('invitation_code', strtoupper($code))
            ->where('is_suspended', false)
            ->firstOrFail();

        $guest->load([
            'wedding.theme',
            'wedding.programItems' => fn($q) => $q->where('is_published', true)->orderBy('sort_order'),
            'wedding.venues' => fn($q) => $q->orderBy('sort_order'),
            'wedding.giftCategories.items',
            'wedding.rules' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'wedding.colorPalette',
            'wedding.inspirationItems' => fn($q) => $q->orderBy('sort_order'),
            'wedding.galleryItems' => fn($q) => $q->orderBy('sort_order'),
            'companions',
            'responses.programItem',
            'reminders' => fn($q) => $q->where('status', 'sent')->latest()->take(10),
        ]);

        return view('guest.personal', compact('guest'));
    }

    /**
     * Soumission RSVP depuis la page personnelle de l'invité
     */
    public function rsvpSubmit(Request $request, string $code)
    {
        $key = 'rsvp_guest_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()->with('error', 'Trop de tentatives. Merci de réessayer dans quelques minutes.');
        }
        RateLimiter::hit($key, 60);

        $guest = Guest::where('invitation_code', strtoupper($code))
            ->where('is_suspended', false)
            ->firstOrFail();

        $wedding = $guest->wedding;

        // Vérifier deadline
        if ($wedding->rsvp_deadline && now()->isAfter($wedding->rsvp_deadline)) {
            return back()->with('error', 'La date limite de réponse est dépassée.');
        }

        // Vérifier modification autorisée
        if ($guest->rsvp_status !== 'pending' && !$wedding->rsvp_modification_allowed) {
            return back()->with('error', 'La modification de réponse n\'est plus autorisée.');
        }

        $data = $request->validate([
            'rsvp_status' => 'required|in:accepted,declined,maybe',
            'companions_count' => 'nullable|integer|min:0|max:20',
            'dietary_restrictions' => 'nullable|string|max:500',
            'message' => 'nullable|string|max:1000',
            'program_responses' => 'nullable|array',
            'program_responses.*' => 'in:attending,not_attending,pending',
            'companions' => 'nullable|array',
            'companions.*.first_name' => 'required_with:companions|string|max:100',
            'companions.*.last_name' => 'nullable|string|max:100',
            'companions.*.dietary_restrictions' => 'nullable|string|max:200',
        ]);

        $guest->update([
            'rsvp_status' => $data['rsvp_status'],
            'companions_count' => $data['companions_count'] ?? 0,
            'dietary_restrictions' => $data['dietary_restrictions'] ?? null,
            'rsvp_at' => now(),
        ]);

        // Réponses par étape
        if (!empty($data['program_responses'])) {
            $guest->responses()->delete();
            foreach ($data['program_responses'] as $itemId => $status) {
                GuestResponse::create([
                    'guest_id' => $guest->id,
                    'wedding_program_item_id' => $itemId,
                    'status' => $status,
                    'message' => $data['message'] ?? null,
                ]);
            }
        } elseif (!empty($data['message'])) {
            $guest->responses()->delete();
            GuestResponse::create([
                'guest_id' => $guest->id,
                'status' => $data['rsvp_status'],
                'message' => $data['message'],
            ]);
        }

        // Accompagnants
        if ($guest->companions_allowed && !empty($data['companions'])) {
            $guest->companions()->delete();
            foreach ($data['companions'] as $companion) {
                GuestCompanion::create([
                    'guest_id' => $guest->id,
                    'first_name' => $companion['first_name'],
                    'last_name' => $companion['last_name'] ?? null,
                    'dietary_restrictions' => $companion['dietary_restrictions'] ?? null,
                ]);
            }
        }

        return redirect()->route('guest.personal', $code)
            ->with('success', $data['rsvp_status'] === 'accepted'
                ? 'Merci ! Votre présence est bien confirmée. Nous avons hâte de vous accueillir !'
                : 'Votre réponse a bien été enregistrée. Merci !');
    }

    /**
     * Lien magique par token (envoyé par email)
     * Redirige vers la page personnelle
     */
    public function magicLink(string $token)
    {
        $invitationToken = InvitationToken::where('token', $token)->first();

        if (!$invitationToken || !$invitationToken->isValid()) {
            abort(404, 'Ce lien est invalide ou a expiré.');
        }

        $invitationToken->update(['is_used' => true]);

        $guest = $invitationToken->guest;

        return redirect()->route('guest.personal', $guest->invitation_code);
    }
}
