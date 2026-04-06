<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use Illuminate\Http\Request;

class PublicWeddingController extends Controller
{
    // Page d'accueil → première invitation publiée
    public function home()
    {
        $wedding = Wedding::where('is_published', true)->latest()->first();

        if (!$wedding) {
            // Aucun mariage publié encore : page d'attente élégante
            return view('public.coming-soon');
        }

        return $this->renderWedding($wedding);
    }

    public function show(string $slug)
    {
        $wedding = Wedding::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return $this->renderWedding($wedding);
    }

    private function renderWedding(Wedding $wedding)
    {
        $wedding->load([
            'theme',
            'sections' => fn($q) => $q->orderBy('sort_order'),
            'programItems' => fn($q) => $q->where('is_published', true)->orderBy('sort_order'),
            'venues' => fn($q) => $q->orderBy('sort_order'),
            'galleryItems' => fn($q) => $q->orderBy('sort_order'),
            'giftCategories.items' => fn($q) => $q->orderBy('sort_order'),
            'rules' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'colorPalette',
            'inspirationItems' => fn($q) => $q->orderBy('sort_order'),
        ]);

        $sectionKeys = $wedding->sections->pluck('is_active', 'key')->toArray();

        return view('public.wedding', compact('wedding', 'sectionKeys'));
    }
}
