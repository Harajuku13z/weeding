<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\WeddingVenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VenueController extends Controller
{
    public function index(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $venues = $wedding->venues;
        return view('admin.venues.index', compact('wedding', 'venues'));
    }

    public function create(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        return view('admin.venues.create', compact('wedding'));
    }

    public function store(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|url',
            'waze_url' => 'nullable|url',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);

        $data['wedding_id'] = $wedding->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('weddings/' . $wedding->id . '/venues', 'public');
        }

        WeddingVenue::create($data);

        return redirect()->route('admin.weddings.venues.index', $wedding)->with('success', 'Lieu ajouté !');
    }

    public function edit(Wedding $wedding, WeddingVenue $venue)
    {
        $this->authorize('update', $wedding);
        return view('admin.venues.edit', compact('wedding', 'venue'));
    }

    public function update(Request $request, Wedding $wedding, WeddingVenue $venue)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|url',
            'waze_url' => 'nullable|url',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('photo')) {
            if ($venue->photo) Storage::disk('public')->delete($venue->photo);
            $data['photo'] = $request->file('photo')->store('weddings/' . $wedding->id . '/venues', 'public');
        }

        $venue->update($data);

        return redirect()->route('admin.weddings.venues.index', $wedding)->with('success', 'Lieu mis à jour !');
    }

    public function destroy(Wedding $wedding, WeddingVenue $venue)
    {
        $this->authorize('update', $wedding);
        if ($venue->photo) Storage::disk('public')->delete($venue->photo);
        $venue->delete();
        return back()->with('success', 'Lieu supprimé.');
    }

    public function show(Wedding $wedding, WeddingVenue $venue)
    {
        return redirect()->route('admin.weddings.venues.edit', [$wedding, $venue]);
    }
}
