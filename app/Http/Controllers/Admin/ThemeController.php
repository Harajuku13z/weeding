<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\WeddingTheme;
use App\Models\WeddingSection;
use App\Models\ColorPaletteItem;
use App\Models\InspirationItem;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    public function edit(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        $theme = $wedding->theme ?? WeddingTheme::create(['wedding_id' => $wedding->id]);
        $palette = $wedding->colorPalette;
        $inspirations = $wedding->inspirationItems;
        $sections = $wedding->sections->keyBy('key');
        return view('admin.theme.edit', compact('wedding', 'theme', 'palette', 'inspirations', 'sections'));
    }

    public function update(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $themeData = $request->validate([
            'color_primary' => 'required|string|max:20',
            'color_secondary' => 'required|string|max:20',
            'color_accent' => 'required|string|max:20',
            'color_background' => 'required|string|max:20',
            'color_text' => 'required|string|max:20',
            'font_title' => 'nullable|string|max:100',
            'font_body' => 'nullable|string|max:100',
            'button_style' => 'nullable|string|max:50',
            'border_radius' => 'nullable|string|max:20',
            'animation_intensity' => 'nullable|in:none,light,medium,heavy',
            'dress_code_style' => 'nullable|string|max:100',
            'dress_code_formality' => 'nullable|string|max:100',
            'dress_code_description' => 'nullable|string',
            'dress_code_men' => 'nullable|string',
            'dress_code_women' => 'nullable|string',
            'dress_code_accessories' => 'nullable|string',
            'forbidden_colors' => 'nullable|string',
            'mood_description' => 'nullable|string',
            'mood_textures' => 'nullable|string',
            'inspirations' => 'nullable|array',
            'inspirations.*' => 'nullable|image|max:5120',
        ]);

        unset($themeData['inspirations']);
        $theme = $wedding->theme ?? WeddingTheme::create(['wedding_id' => $wedding->id]);
        $theme->update($themeData);

        // Palette de couleurs
        if ($request->has('palette')) {
            $wedding->colorPalette()->delete();
            foreach ($request->palette as $i => $item) {
                if (!empty($item['name']) && !empty($item['hex_color'])) {
                    ColorPaletteItem::create([
                        'wedding_id' => $wedding->id,
                        'name' => $item['name'],
                        'hex_color' => $item['hex_color'],
                        'description' => $item['description'] ?? null,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        // Inspirations (upload images) — compression Intervention avant enregistrement
        $inspirationFiles = $request->file('inspirations');
        $inspirationCount = 0;
        if (is_array($inspirationFiles)) {
            $imageService = app(ImageCompressionService::class);
            $inspirationsDir = 'weddings/' . $wedding->id . '/inspirations';
            $sortOrder = (int) $wedding->inspirationItems()->max('sort_order');
            foreach ($inspirationFiles as $i => $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }
                $path = $imageService->compressForInspiration($file, $inspirationsDir);
                InspirationItem::create([
                    'wedding_id' => $wedding->id,
                    'image' => $path,
                    'caption' => $request->input('inspiration_captions.' . $i),
                    'category' => $request->input('inspiration_categories.' . $i, 'general'),
                    'sort_order' => ++$sortOrder,
                ]);
                $inspirationCount++;
            }
        }

        // Visibilité des sections (ex. afficher ou non la section Cadeaux)
        $editableSectionKeys = ['story', 'gallery', 'details', 'program', 'venues', 'dresscode', 'gifts', 'rules', 'rsvp', 'accommodation'];
        if ($request->has('section_visible')) {
            $visible = (array) $request->section_visible;
            foreach ($wedding->sections as $section) {
                if (in_array($section->key, $editableSectionKeys)) {
                    $section->is_active = in_array($section->key, $visible);
                    $section->save();
                }
            }
        }

        $message = 'Thème mis à jour !';
        if ($inspirationCount > 0) {
            $message .= ' ' . $inspirationCount . ' photo(s) d\'inspiration enregistrée(s).';
        }
        return redirect()->route('admin.weddings.theme.edit', $wedding)
            ->with('success', $message);
    }

    public function destroyInspiration(Wedding $wedding, InspirationItem $inspirationItem)
    {
        $this->authorize('update', $wedding);
        if ($inspirationItem->wedding_id !== $wedding->id) {
            abort(404);
        }
        if ($inspirationItem->image && Storage::disk('public')->exists($inspirationItem->image)) {
            Storage::disk('public')->delete($inspirationItem->image);
        }
        $inspirationItem->delete();
        return redirect()->route('admin.weddings.theme.edit', $wedding)
            ->with('success', 'Photo d\'inspiration supprimée.');
    }
}
