<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function index(Request $request, Wedding $wedding)
    {
        $this->authorize('view', $wedding);

        $query = $wedding->guests()->with('reminders');

        if ($request->status) $query->where('rsvp_status', $request->status);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->tag) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $guests = $query->latest()->paginate(20);
        $stats = $wedding->getRsvpStats();

        return view('admin.guests.index', compact('wedding', 'guests', 'stats'));
    }

    public function create(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        return view('admin.guests.create', compact('wedding'));
    }

    public function store(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:20',
            'companions_allowed' => 'boolean',
            'max_companions' => 'nullable|integer|min:0',
            'notes_admin' => 'nullable|string',
            'contact_channel' => 'nullable|in:email,sms,whatsapp',
            'personal_message' => 'nullable|string|max:500',
        ]);

        $data['wedding_id'] = $wedding->id;
        $data['companions_allowed'] = $request->boolean('companions_allowed');

        $guest = Guest::create($data);

        return redirect()->route('admin.weddings.guests.index', $wedding)
            ->with('success', 'Invité ajouté : ' . $guest->full_name);
    }

    public function show(Wedding $wedding, Guest $guest)
    {
        $this->authorize('view', $wedding);
        $guest->load(['companions', 'responses.programItem', 'reminders']);
        return view('admin.guests.show', compact('wedding', 'guest'));
    }

    public function edit(Wedding $wedding, Guest $guest)
    {
        $this->authorize('update', $wedding);
        return view('admin.guests.edit', compact('wedding', 'guest'));
    }

    public function update(Request $request, Wedding $wedding, Guest $guest)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:200',
            'phone' => 'nullable|string|max:20',
            'companions_allowed' => 'boolean',
            'max_companions' => 'nullable|integer|min:0',
            'notes_admin' => 'nullable|string',
            'contact_channel' => 'nullable|in:email,sms,whatsapp',
            'rsvp_status' => 'nullable|in:pending,accepted,declined,maybe',
            'personal_message' => 'nullable|string|max:500',
        ]);

        $data['companions_allowed'] = $request->boolean('companions_allowed');
        $guest->update($data);

        return redirect()->route('admin.weddings.guests.show', [$wedding, $guest])
            ->with('success', 'Invité mis à jour !');
    }

    public function destroy(Wedding $wedding, Guest $guest)
    {
        $this->authorize('update', $wedding);
        $guest->delete();
        return redirect()->route('admin.weddings.guests.index', $wedding)
            ->with('success', 'Invité supprimé.');
    }

    public function suspend(Wedding $wedding, Guest $guest)
    {
        $this->authorize('update', $wedding);
        $guest->update(['is_suspended' => !$guest->is_suspended]);
        return back()->with('success', $guest->is_suspended ? 'Invité suspendu.' : 'Invité réactivé.');
    }

    public function export(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $guests = $wedding->guests()->with('companions')->get();

        $csv = "Prénom,Nom,Email,Téléphone,Statut RSVP,Accompagnants,Restrictions alimentaires\n";
        foreach ($guests as $guest) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s",%d,"%s"' . "\n",
                $guest->first_name,
                $guest->last_name,
                $guest->email ?? '',
                $guest->phone ?? '',
                $guest->status_label,
                $guest->companions_count,
                $guest->dietary_restrictions ?? ''
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invites-' . $wedding->slug . '.csv"',
        ]);
    }
}
