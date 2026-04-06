<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\WeddingProgramItem;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $items = $wedding->programItems;
        return view('admin.program.index', compact('wedding', 'items'));
    }

    public function create(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        return view('admin.program.create', compact('wedding'));
    }

    public function store(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'venue_name' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['wedding_id'] = $wedding->id;
        $data['is_published'] = $request->boolean('is_published');

        WeddingProgramItem::create($data);

        return redirect()->route('admin.weddings.program.index', $wedding)
            ->with('success', 'Étape du programme ajoutée !');
    }

    public function edit(Wedding $wedding, WeddingProgramItem $program)
    {
        $this->authorize('update', $wedding);
        return view('admin.program.edit', compact('wedding', 'program'));
    }

    public function update(Request $request, Wedding $wedding, WeddingProgramItem $program)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'venue_name' => 'nullable|string|max:200',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'is_published' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $program->update($data);

        return redirect()->route('admin.weddings.program.index', $wedding)
            ->with('success', 'Étape mise à jour !');
    }

    public function destroy(Wedding $wedding, WeddingProgramItem $program)
    {
        $this->authorize('update', $wedding);
        $program->delete();
        return back()->with('success', 'Étape supprimée.');
    }

    public function show(Wedding $wedding, WeddingProgramItem $program)
    {
        return redirect()->route('admin.weddings.program.edit', [$wedding, $program]);
    }
}
