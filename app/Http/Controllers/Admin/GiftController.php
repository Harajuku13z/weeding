<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\GiftItem;
use App\Models\GiftCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GiftController extends Controller
{
    public function index(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $categories = $wedding->giftCategories()->with('items')->get();
        $uncategorized = $wedding->giftItems()->whereNull('gift_category_id')->get();
        return view('admin.gifts.index', compact('wedding', 'categories', 'uncategorized'));
    }

    public function create(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        $categories = $wedding->giftCategories;
        return view('admin.gifts.create', compact('wedding', 'categories'));
    }

    public function store(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'external_link' => 'nullable|url',
            'price' => 'nullable|numeric|min:0',
            'gift_category_id' => 'nullable|exists:gift_categories,id',
            'is_reserved' => 'boolean',
            'free_contribution' => 'boolean',
            'instructions' => 'nullable|string',
        ]);

        $data['wedding_id'] = $wedding->id;
        $data['is_reserved'] = $request->boolean('is_reserved');
        $data['free_contribution'] = $request->boolean('free_contribution');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('weddings/' . $wedding->id . '/gifts', 'public');
        }

        // Création catégorie si nouvelle
        if ($request->new_category) {
            $cat = GiftCategory::create(['wedding_id' => $wedding->id, 'name' => $request->new_category]);
            $data['gift_category_id'] = $cat->id;
        }

        GiftItem::create($data);

        return redirect()->route('admin.weddings.gifts.index', $wedding)->with('success', 'Cadeau ajouté !');
    }

    public function edit(Wedding $wedding, GiftItem $gift)
    {
        $this->authorize('update', $wedding);
        $categories = $wedding->giftCategories;
        return view('admin.gifts.edit', compact('wedding', 'gift', 'categories'));
    }

    public function update(Request $request, Wedding $wedding, GiftItem $gift)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'external_link' => 'nullable|url',
            'price' => 'nullable|numeric|min:0',
            'gift_category_id' => 'nullable|exists:gift_categories,id',
            'is_reserved' => 'boolean',
            'free_contribution' => 'boolean',
            'instructions' => 'nullable|string',
        ]);

        $data['is_reserved'] = $request->boolean('is_reserved');
        $data['free_contribution'] = $request->boolean('free_contribution');

        if ($request->hasFile('image')) {
            if ($gift->image) Storage::disk('public')->delete($gift->image);
            $data['image'] = $request->file('image')->store('weddings/' . $wedding->id . '/gifts', 'public');
        }

        $gift->update($data);

        return redirect()->route('admin.weddings.gifts.index', $wedding)->with('success', 'Cadeau mis à jour !');
    }

    public function destroy(Wedding $wedding, GiftItem $gift)
    {
        $this->authorize('update', $wedding);
        if ($gift->image) Storage::disk('public')->delete($gift->image);
        $gift->delete();
        return back()->with('success', 'Cadeau supprimé.');
    }

    public function show(Wedding $wedding, GiftItem $gift)
    {
        return redirect()->route('admin.weddings.gifts.edit', [$wedding, $gift]);
    }
}
