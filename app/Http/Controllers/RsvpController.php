<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Models\Guest;
use App\Models\GuestResponse;
use App\Models\GuestCompanion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RsvpController extends Controller
{
    public function show(string $wedding, string $code)
    {
        $wedding = Wedding::where('slug', $wedding)->where('is_published', true)->firstOrFail();
        $guest = Guest::where('invitation_code', $code)
            ->where('wedding_id', $wedding->id)
            ->where('is_suspended', false)
            ->firstOrFail();

        $programItems = $wedding->programItems()->where('is_published', true)->orderBy('sort_order')->get();

        return view('public.rsvp', compact('wedding', 'guest', 'programItems'));
    }

    public function submit(Request $request, string $wedding)
    {
        $key = 'rsvp_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()->with('error', 'Trop de tentatives. Merci de réessayer plus tard.');
        }
        RateLimiter::hit($key, 60);

        $weddingModel = Wedding::where('slug', $wedding)->where('is_published', true)->firstOrFail();

        $data = $request->validate([
            'invitation_code' => 'required|string',
            'rsvp_status' => 'required|in:accepted,declined,maybe',
            'companions_count' => 'nullable|integer|min:0',
            'dietary_restrictions' => 'nullable|string|max:500',
            'message' => 'nullable|string|max:1000',
            'program_responses' => 'nullable|array',
            'program_responses.*' => 'in:attending,not_attending,pending',
            'companions' => 'nullable|array',
            'companions.*.first_name' => 'required_with:companions|string|max:100',
            'companions.*.last_name' => 'nullable|string|max:100',
            'companions.*.dietary_restrictions' => 'nullable|string|max:200',
        ]);

        $guest = Guest::where('invitation_code', $data['invitation_code'])
            ->where('wedding_id', $weddingModel->id)
            ->where('is_suspended', false)
            ->firstOrFail();

        // Vérifier deadline RSVP
        if ($weddingModel->rsvp_deadline && now()->isAfter($weddingModel->rsvp_deadline)) {
            return back()->with('error', 'La date limite de réponse est dépassée.');
        }

        // Vérifier modification autorisée
        if ($guest->rsvp_status !== 'pending' && !$weddingModel->rsvp_modification_allowed) {
            return back()->with('error', 'La modification de réponse n\'est pas autorisée.');
        }

        // Mettre à jour le statut
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

        return redirect()->route('rsvp.confirmation', $guest)
            ->with('success', 'Votre réponse a bien été enregistrée. Merci !');
    }

    public function confirmation(Guest $guest)
    {
        $guest->load(['wedding.theme', 'companions', 'responses.programItem']);
        return view('public.rsvp-confirmation', compact('guest'));
    }
}
