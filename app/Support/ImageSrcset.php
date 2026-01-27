<?php

namespace App\Support;

class ImageSrcset
{
    public static function from(?string $src, array $widths = [], ?string $format = null): ?string
    {
        if (! $src) {
            return null;
        }

        $extension = pathinfo($src, PATHINFO_EXTENSION);
        $format = $format ?: $extension;

        $entries = self::entriesForFormat($src, $format);

        $parts = [];
        foreach ($entries as $width => $path) {
            if ($widths && ! in_array((int) $width, $widths, true)) {
                continue;
            }
            $parts[] = $path.' '.$width.'w';
        }

        // Always include the original image if it matches the format or if no format variants found
        // Assign it a high width to ensure high-quality on desktop
        if ($format === $extension) {
            $originalWidth = 1920; // Default "high resolution" width
            $parts[] = $src.' '.$originalWidth.'w';
        }

        // Sort by width to keep browsers happy
        usort($parts, function ($a, $b) {
            preg_match('/ (\d+)w$/', $a, $am);
            preg_match('/ (\d+)w$/', $b, $bm);

            return (int) $am[1] <=> (int) $bm[1];
        });

        return $parts ? implode(', ', array_unique($parts)) : null;
    }

    public static function entriesForFormat(?string $src, string $format = 'webp'): array
    {
        if (! $src) {
            return [];
        }

        $manifest = self::manifest();
        $key = ltrim($src, '/');
        $entries = $manifest['images'][$key] ?? null;
        if (! $entries || ! is_array($entries)) {
            return [];
        }

        if (isset($entries['webp']) || isset($entries['avif'])) {
            $formatEntries = $entries[$format] ?? [];
            if (! is_array($formatEntries)) {
                return [];
            }

            return self::normalizeEntries($formatEntries, $format);
        }

        return $format === 'webp' ? self::normalizeEntries($entries, $format) : [];
    }

    public static function manifest(): array
    {
        $path = public_path('images/variants/manifest.json');
        if (! is_file($path)) {
            return ['version' => 2, 'images' => []];
        }

        $data = json_decode(file_get_contents($path), true);
        if (! is_array($data)) {
            return ['version' => 2, 'images' => []];
        }

        $data['version'] = $data['version'] ?? 2;
        $data['images'] = $data['images'] ?? [];

        return $data;
    }

    private static function normalizeEntries(array $entries, string $format): array
    {
        if (! array_is_list($entries)) {
            return $entries;
        }

        $normalized = [];
        foreach ($entries as $path) {
            if (! is_string($path)) {
                continue;
            }
            $width = self::extractWidthFromPath($path, $format);
            if ($width === null) {
                continue;
            }
            $normalized[$width] = $path;
        }

        ksort($normalized);

        return $normalized;
    }

    private static function extractWidthFromPath(string $path, string $format): ?int
    {
        $pattern = '/-(\d+)\.'.preg_quote($format, '/').'$/i';
        if (preg_match($pattern, $path, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
