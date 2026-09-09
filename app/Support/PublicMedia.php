<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicMedia
{
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        // Relative URL so images work on tetri.test / localhost / any host.
        return '/storage/'.ltrim(str_replace('\\', '/', $path), '/');
    }

    public static function exists(?string $path): bool
    {
        return filled($path) && Storage::disk('public')->exists($path);
    }
}
