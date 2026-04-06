<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $wedding = $user->weddings()->with(['guests', 'reminders'])->latest()->first();

        $stats = $wedding ? $wedding->getRsvpStats() : [];
        $recentGuests = $wedding
            ? $wedding->guests()->whereNotNull('rsvp_at')->latest('rsvp_at')->take(10)->get()
            : collect();
        $remindersSent = $wedding ? $wedding->reminders()->where('status', 'sent')->count() : 0;
        $weddings = $user->weddings()->latest()->get();

        return view('admin.dashboard', compact('wedding', 'stats', 'recentGuests', 'remindersSent', 'weddings'));
    }
}
