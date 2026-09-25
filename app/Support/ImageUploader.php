<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Previously, "store the new file, and delete the old one if replacing"
 * was written out three separate times in ProductController (store,
 * update, destroy), each slightly differently. This class is the one
 * place that logic lives now.
 */
class ImageUploader
{
    /**
     * Store a freshly uploaded file, deleting $existingPath first if given.
     * Returns the new stored path, or the untouched $existingPath if no
     * new file was uploaded.
     */
    public static function replace(?UploadedFile $file, ?string $existingPath, string $directory = 'products'): ?string
    {
        if (! $file) {
            return $existingPath;
        }

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        return $file->store($directory, 'public');
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /** Public URL for a stored path, or null if there isn't one. */
    public static function url(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }
}