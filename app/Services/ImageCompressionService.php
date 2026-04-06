<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\JpegEncoder;

/**
 * Compresse et redimensionne les images avant enregistrement (Intervention Image).
 * Réduit la taille des fichiers pour éviter les erreurs POST trop volumineux.
 */
class ImageCompressionService
{
    /** Largeur max en pixels (galerie, inspirations). */
    public const MAX_WIDTH = 1920;

    /** Qualité JPEG (0-100). */
    public const JPEG_QUALITY = 82;

    /** Largeur max pour les inspirations tenues (plus petit). */
    public const MAX_WIDTH_INSPIRATIONS = 1200;

    /**
     * Compresse une image uploadée et l'enregistre sur le disque public.
     *
     * @param  UploadedFile  $file  Fichier image uploadé
     * @param  string  $directory  Dossier de destination (ex: weddings/1/gallery)
     * @param  int|null  $maxWidth  Largeur max (null = MAX_WIDTH)
     * @return string  Chemin relatif du fichier enregistré
     */
    public function compressAndStore(UploadedFile $file, string $directory, ?int $maxWidth = null): string
    {
        $maxWidth ??= self::MAX_WIDTH;
        $filename = Str::random(40) . '.jpg';

        $image = Image::read($file->getRealPath());
        $image->scaleDown($maxWidth, null);
        $encoded = $image->encode(new JpegEncoder(quality: self::JPEG_QUALITY));

        $path = $directory . '/' . $filename;
        Storage::disk('public')->put($path, $encoded->toString());

        return $path;
    }

    /**
     * Compresse une image pour la galerie (max 1920px).
     */
    public function compressForGallery(UploadedFile $file, string $directory): string
    {
        return $this->compressAndStore($file, $directory, self::MAX_WIDTH);
    }

    /**
     * Compresse une image pour les inspirations tenues (max 1200px).
     */
    public function compressForInspiration(UploadedFile $file, string $directory): string
    {
        return $this->compressAndStore($file, $directory, self::MAX_WIDTH_INSPIRATIONS);
    }
}
