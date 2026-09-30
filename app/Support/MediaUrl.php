<?php

namespace App\Support;

class MediaUrl
{
    public static function for(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $base = rtrim(config('app.url'), '/');

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return $base.'/storage/'.ltrim($path, '/');
    }
}