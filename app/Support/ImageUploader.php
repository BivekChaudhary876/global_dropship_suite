<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class ImageUploader
{
    /** Store a freshly uploaded file, replacing an existing one if there is one. */
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

    /**
     * Store several freshly uploaded files at once (the multi-image
     * product gallery). Returns the stored paths in the same order the
     * files were uploaded in.
     *
     * @param  UploadedFile[]  $files
     * @return string[]
     */
    public static function storeMany(array $files, string $directory = 'products'): array
    {
        return array_map(
            fn (UploadedFile $file) => $file->store($directory, 'public'),
            $files,
        );
    }

    /** Delete several stored paths at once. */
    public static function deleteMany(iterable $paths): void
    {
        foreach ($paths as $path) {
            static::delete($path);
        }
    }

    /** Public URL for a stored path, or null if there isn't one. */
    public static function url(?string $path): ?string
    {
        return $path ? asset('storage/'.$path) : null;
    }
}