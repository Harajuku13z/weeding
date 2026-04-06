<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\WeddingTheme;
use App\Models\WeddingSection;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class WeddingController extends Controller
{
    public function index(Request $request)
    {
        $weddings = $request->user()->weddings()->latest()->paginate(10);
        return view('admin.weddings.index', compact('weddings'));
    }

    public function create()
    {
        return view('admin.weddings.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bride_name' => 'required|string|max:100',
            'groom_name' => 'required|string|max:100',
            'wedding_date' => 'nullable|date',
            'quote' => 'nullable|string|max:500',
            'intro_text' => 'nullable|string',
            'welcome_message' => 'nullable|string',
        ]);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['bride_name'] . '-' . $data['groom_name'] . '-' . Str::random(4));

        $wedding = Wedding::create($data);

        // Créer le thème par défaut
        WeddingTheme::create(['wedding_id' => $wedding->id]);

        // Créer les sections par défaut
        $this->createDefaultSections($wedding);

        return redirect()->route('admin.weddings.show', $wedding)
            ->with('success', 'Mariage créé avec succès !');
    }

    public function show(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $stats = $wedding->getRsvpStats();
        $wedding->load(['guests', 'programItems', 'venues', 'galleryItems', 'theme']);
        return view('admin.weddings.show', compact('wedding', 'stats'));
    }

    public function edit(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        return view('admin.weddings.edit', compact('wedding'));
    }

    public function update(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'bride_name' => 'required|string|max:100',
            'groom_name' => 'required|string|max:100',
            'wedding_date' => 'nullable|date',
            'quote' => 'nullable|string|max:500',
            'intro_text' => 'nullable|string',
            'welcome_message' => 'nullable|string',
            'story_text' => 'nullable|string',
            'seo_title' => 'nullable|string|max:100',
            'seo_description' => 'nullable|string|max:300',
            'envelope_animation' => 'boolean',
            'floral_decor' => 'boolean',
            'music_enabled' => 'boolean',
            'rsvp_modification_allowed' => 'boolean',
            'rsvp_deadline' => 'nullable|date',
            'accommodation_info' => 'nullable|string',
            'accommodation_details' => 'nullable|string',
            'logo' => 'nullable|image|max:5120',
            'hero_image' => 'nullable|image|max:5120',
            'couple_photo' => 'nullable|image|max:5120',
            'social_image' => 'nullable|image|max:5120',
        ]);

        // Retirer les champs fichier de $data (on les gère après)
        unset($data['logo'], $data['hero_image'], $data['couple_photo'], $data['social_image']);

        // Gestion des booleans depuis les checkboxes
        $data['envelope_animation'] = $request->boolean('envelope_animation');
        $data['floral_decor'] = $request->boolean('floral_decor');
        $data['music_enabled'] = $request->boolean('music_enabled');
        $data['rsvp_modification_allowed'] = $request->boolean('rsvp_modification_allowed');

        // Upload images (compression Intervention avant enregistrement)
        $imageService = app(ImageCompressionService::class);
        $uploadDir = 'weddings/' . $wedding->id;
        foreach (['logo', 'hero_image', 'couple_photo', 'social_image'] as $field) {
            if ($request->hasFile($field)) {
                if ($wedding->$field) {
                    Storage::disk('public')->delete($wedding->$field);
                }
                $data[$field] = $imageService->compressAndStore($request->file($field), $uploadDir, 1920);
            }
        }

        $wedding->update($data);

        return redirect()->route('admin.weddings.edit', $wedding)
            ->with('success', 'Mariage mis à jour !');
    }

    public function destroy(Wedding $wedding)
    {
        $this->authorize('delete', $wedding);
        $wedding->delete();
        return redirect()->route('admin.weddings.index')->with('success', 'Mariage supprimé.');
    }

    public function publish(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        $wedding->update([
            'is_published' => !$wedding->is_published,
            'is_draft' => $wedding->is_published,
        ]);
        $msg = $wedding->is_published ? 'Mariage publié !' : 'Mariage mis en brouillon.';
        return back()->with('success', $msg);
    }

    public function duplicate(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $new = $wedding->replicate();
        $new->slug = $wedding->slug . '-copie-' . Str::random(4);
        $new->is_published = false;
        $new->is_draft = true;
        $new->bride_name = $wedding->bride_name . ' (copie)';
        $new->save();

        if ($wedding->theme) {
            $theme = $wedding->theme->replicate();
            $theme->wedding_id = $new->id;
            $theme->save();
        }

        return redirect()->route('admin.weddings.show', $new)
            ->with('success', 'Mariage dupliqué !');
    }

    private function createDefaultSections(Wedding $wedding): void
    {
        $sections = [
            ['key' => 'hero', 'title' => 'Hero', 'sort_order' => 1],
            ['key' => 'story', 'title' => 'Notre histoire', 'sort_order' => 2],
            ['key' => 'gallery', 'title' => 'Galerie', 'sort_order' => 3],
            ['key' => 'details', 'title' => 'Détails', 'sort_order' => 4],
            ['key' => 'program', 'title' => 'Programme', 'sort_order' => 5],
            ['key' => 'venues', 'title' => 'Lieux', 'sort_order' => 6],
            ['key' => 'dresscode', 'title' => 'Dress code & ambiance', 'sort_order' => 7],
            ['key' => 'inspiration', 'title' => 'Inspirations', 'sort_order' => 8],
            ['key' => 'gifts', 'title' => 'Liste de cadeaux', 'sort_order' => 9],
            ['key' => 'rules', 'title' => 'Consignes', 'sort_order' => 10],
            ['key' => 'faq', 'title' => 'FAQ', 'sort_order' => 11],
            ['key' => 'rsvp', 'title' => 'RSVP', 'sort_order' => 12],
            ['key' => 'accommodation', 'title' => 'Hébergement', 'sort_order' => 13],
            ['key' => 'contact', 'title' => 'Contact', 'sort_order' => 14],
        ];

        foreach ($sections as $section) {
            WeddingSection::create(array_merge($section, ['wedding_id' => $wedding->id]));
        }
    }
}
