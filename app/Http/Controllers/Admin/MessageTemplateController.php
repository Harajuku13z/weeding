<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $templates = $wedding->messageTemplates;
        return view('admin.templates.index', compact('wedding', 'templates'));
    }

    public function create(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        return view('admin.templates.create', compact('wedding'));
    }

    public function store(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'channel' => 'required|in:email,sms,whatsapp',
            'subject' => 'nullable|string|max:300',
            'body' => 'required|string',
            'type' => 'required|in:reminder,invitation,confirmation',
        ]);

        $data['wedding_id'] = $wedding->id;
        MessageTemplate::create($data);

        return redirect()->route('admin.weddings.templates.index', $wedding)->with('success', 'Modèle créé !');
    }

    public function edit(Wedding $wedding, MessageTemplate $template)
    {
        $this->authorize('update', $wedding);
        return view('admin.templates.edit', compact('wedding', 'template'));
    }

    public function update(Request $request, Wedding $wedding, MessageTemplate $template)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'channel' => 'required|in:email,sms,whatsapp',
            'subject' => 'nullable|string|max:300',
            'body' => 'required|string',
            'type' => 'required|in:reminder,invitation,confirmation',
        ]);

        $template->update($data);

        return redirect()->route('admin.weddings.templates.index', $wedding)->with('success', 'Modèle mis à jour !');
    }

    public function destroy(Wedding $wedding, MessageTemplate $template)
    {
        $this->authorize('update', $wedding);
        $template->delete();
        return back()->with('success', 'Modèle supprimé.');
    }

    public function show(Wedding $wedding, MessageTemplate $template)
    {
        return redirect()->route('admin.weddings.templates.edit', [$wedding, $template]);
    }
}
