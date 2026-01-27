<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicFile
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $clean = self::sanitize($path);
        if (! $clean) {
            return null;
        }

        self::ensure($clean);

        $disk = Storage::disk('public');

        return $disk->exists($clean)
            ? $disk->url($clean)
            : null;
    }

    public static function ensure(?string $path): void
    {
        $clean = self::sanitize($path);

        if (! $clean) {
            return;
        }

        $publicDisk = Storage::disk('public');

        if ($publicDisk->exists($clean)) {
            ImageVariants::generateForPublicPath($clean);

            return;
        }

        $defaultDisk = Storage::disk(config('filesystems.default', 'local'));
        $candidates = [$clean];

        if (! Str::startsWith($clean, 'private/')) {
            $candidates[] = 'private/'.$clean;
        }

        foreach ($candidates as $candidate) {
            if (! $defaultDisk->exists($candidate)) {
                continue;
            }

            $publicDisk->put($clean, $defaultDisk->get($candidate));
            ImageVariants::generateForPublicPath($clean);
            break;
        }
    }

    public static function sanitize(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $trimmed = ltrim($path, '/');
        if (Str::startsWith($trimmed, 'storage/')) {
            $trimmed = Str::after($trimmed, 'storage/');
        }

        return $trimmed !== '' ? $trimmed : null;
    }
}
