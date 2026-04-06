<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\Guest;
use App\Models\Reminder;
use App\Models\MessageTemplate;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function __construct(private ReminderService $reminderService) {}

    public function index(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $reminders = $wedding->reminders()->with('guest')->latest()->paginate(20);
        $templates = $wedding->messageTemplates;
        $pendingGuests = $wedding->guests()->where('rsvp_status', 'pending')->get();
        $maybeGuests = $wedding->guests()->where('rsvp_status', 'maybe')->get();
        return view('admin.reminders.index', compact('wedding', 'reminders', 'templates', 'pendingGuests', 'maybeGuests'));
    }

    public function send(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'channel' => 'required|in:email,sms,whatsapp',
            'template_id' => 'nullable|exists:message_templates,id',
            'message' => 'nullable|string',
        ]);

        $guest = Guest::findOrFail($data['guest_id']);
        $template = $data['template_id'] ? MessageTemplate::findOrFail($data['template_id']) : null;
        $message = $template ? $template->parseFor($guest, $wedding) : $data['message'];

        $reminder = $this->reminderService->send($wedding, $guest, $data['channel'], $message);

        return back()->with(
            $reminder->status === 'sent' ? 'success' : 'error',
            $reminder->status === 'sent' ? 'Relance envoyée !' : 'Échec de l\'envoi : ' . $reminder->error_message
        );
    }

    public function sendBulk(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'segment' => 'required|in:pending,maybe,all_no_response',
            'channel' => 'required|in:email,sms,whatsapp',
            'template_id' => 'nullable|exists:message_templates,id',
            'message' => 'nullable|string',
        ]);

        $query = $wedding->guests();
        if ($data['segment'] === 'pending') $query->where('rsvp_status', 'pending');
        elseif ($data['segment'] === 'maybe') $query->where('rsvp_status', 'maybe');
        else $query->whereIn('rsvp_status', ['pending', 'maybe']);

        $guests = $query->where('is_suspended', false)->get();
        $template = $data['template_id'] ? MessageTemplate::findOrFail($data['template_id']) : null;

        $sent = 0;
        foreach ($guests as $guest) {
            $message = $template ? $template->parseFor($guest, $wedding) : $data['message'];
            $reminder = $this->reminderService->send($wedding, $guest, $data['channel'], $message);
            if ($reminder->status === 'sent') $sent++;
        }

        return back()->with('success', "{$sent} relance(s) envoyée(s) sur {$guests->count()} invité(s).");
    }

    public function preview(Request $request, Wedding $wedding)
    {
        $template = MessageTemplate::findOrFail($request->template_id);
        $guest = $wedding->guests()->first();
        $parsed = $guest ? $template->parseFor($guest, $wedding) : $template->body;
        return response()->json(['preview' => $parsed]);
    }
}
