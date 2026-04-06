<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\GalleryItem;
use App\Services\ImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $items = $wedding->galleryItems;
        return view('admin.gallery.index', compact('wedding', 'items'));
    }

    public function create(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        return view('admin.gallery.create', compact('wedding'));
    }

    public function store(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $galleryDir = 'weddings/' . $wedding->id . '/gallery';

        // Récupérer les fichiers (name="images[]" ou "videos[]")
        $imageFiles = $this->normalizeFileArray($request->file('images'));
        $videoFiles = $this->normalizeFileArray($request->file('videos'));

        if (empty($imageFiles) && empty($videoFiles)) {
            return back()->withErrors(['images' => 'Ajoutez au moins une photo ou une vidéo.']);
        }

        // Validation : chaque photo max 5 Mo, chaque vidéo MP4 max 100 Mo
        foreach ($imageFiles as $i => $file) {
            if ($file->getSize() > 5120 * 1024) {
                return back()->withErrors(['images' => 'Chaque photo doit faire au maximum 5 Mo.']);
            }
            $mime = $file->getMimeType();
            if (! str_starts_with($mime, 'image/')) {
                return back()->withErrors(['images' => 'Les photos doivent être des images (JPG, PNG, WebP, etc.).']);
            }
        }
        foreach ($videoFiles as $i => $file) {
            if ($file->getSize() > 102400 * 1024) {
                return back()->withErrors(['videos' => 'Chaque vidéo doit faire au maximum 100 Mo.']);
            }
            if ($file->getMimeType() !== 'video/mp4') {
                return back()->withErrors(['videos' => 'Les vidéos doivent être au format MP4.']);
            }
        }

        $sortOrder = $wedding->galleryItems()->count();
        $imageService = app(ImageCompressionService::class);

        foreach ($imageFiles as $i => $file) {
            try {
                $path = $imageService->compressForGallery($file, $galleryDir);
            } catch (\Throwable $e) {
                $path = $file->store($galleryDir, 'public');
            }
            GalleryItem::create([
                'wedding_id' => $wedding->id,
                'image' => $path,
                'video' => null,
                'caption' => $request->input('captions.' . $i),
                'sort_order' => $sortOrder++,
            ]);
        }

        foreach ($videoFiles as $i => $file) {
            $path = $file->store($galleryDir, 'public');
            GalleryItem::create([
                'wedding_id' => $wedding->id,
                'image' => null,
                'video' => $path,
                'caption' => $request->input('video_captions.' . $i),
                'sort_order' => $sortOrder++,
            ]);
        }

        return redirect()->route('admin.weddings.gallery.index', $wedding)
            ->with('success', 'Médias ajoutés !');
    }

    /**
     * Retourne un tableau d'UploadedFile valides (gère name="images[]" ou fichier unique).
     */
    private function normalizeFileArray($input): array
    {
        if ($input === null) {
            return [];
        }
        if (! is_array($input)) {
            return ($input instanceof \Illuminate\Http\UploadedFile && $input->isValid()) ? [$input] : [];
        }
        $out = [];
        foreach ($input as $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                $out[] = $file;
            }
        }
        return $out;
    }

    public function edit(Wedding $wedding, GalleryItem $gallery)
    {
        $this->authorize('update', $wedding);
        return view('admin.gallery.edit', compact('wedding', 'gallery'));
    }

    public function update(Request $request, Wedding $wedding, GalleryItem $gallery)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'caption' => 'nullable|string|max:300',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'video' => 'nullable|file|mimetypes:video/mp4|max:102400',
        ]);

        unset($data['image'], $data['video']);

        if ($request->hasFile('image')) {
            if ($gallery->image) Storage::disk('public')->delete($gallery->image);
            if ($gallery->video) { Storage::disk('public')->delete($gallery->video); $data['video'] = null; }
            $data['image'] = app(ImageCompressionService::class)->compressForGallery(
                $request->file('image'),
                'weddings/' . $wedding->id . '/gallery'
            );
        }
        if ($request->hasFile('video')) {
            if ($gallery->video) Storage::disk('public')->delete($gallery->video);
            if ($gallery->image) { Storage::disk('public')->delete($gallery->image); $data['image'] = null; }
            $data['video'] = $request->file('video')->store('weddings/' . $wedding->id . '/gallery', 'public');
        }

        $gallery->update($data);
        return redirect()->route('admin.weddings.gallery.index', $wedding)->with('success', 'Média mis à jour !');
    }

    public function destroy(Wedding $wedding, GalleryItem $gallery)
    {
        $this->authorize('update', $wedding);
        if ($gallery->image) Storage::disk('public')->delete($gallery->image);
        if ($gallery->video) Storage::disk('public')->delete($gallery->video);
        $gallery->delete();
        return back()->with('success', 'Élément supprimé.');
    }

    public function show(Wedding $wedding, GalleryItem $gallery)
    {
        return redirect()->route('admin.weddings.gallery.edit', [$wedding, $gallery]);
    }

    public function reorder(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        foreach ($request->order as $i => $id) {
            GalleryItem::where('id', $id)->update(['sort_order' => $i]);
        }
        return response()->json(['success' => true]);
    }
}
