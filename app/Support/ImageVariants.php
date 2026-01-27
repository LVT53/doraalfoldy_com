<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageVariants
{
    public static function generateForUpload(string|array|null $state, array $widths = [240, 320, 360, 480, 640, 720, 960, 1280, 1600, 1920]): void
    {
        foreach (self::extractPaths($state) as $path) {
            self::generateForPublicPath($path, $widths);
        }
    }

    public static function generateForPublicPath(?string $path, array $widths = [240, 320, 360, 480, 640, 720, 960, 1280, 1600, 1920]): void
    {
        if (! $path) {
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return;
        }

        $clean = ltrim($path, '/');
        if (Str::startsWith($clean, 'storage/')) {
            $clean = Str::after($clean, 'storage/');
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($clean)) {
            Log::debug('Image variants skipped: source not found on public disk.', [
                'path' => $clean,
            ]);

            return;
        }

        $ext = strtolower(pathinfo($clean, PATHINFO_EXTENSION));
        if (! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            Log::debug('Image variants skipped: unsupported extension.', [
                'path' => $clean,
                'extension' => $ext,
            ]);

            return;
        }

        $canWebp = function_exists('imagewebp');
        $canAvifGd = function_exists('imageavif');
        $canAvifImagick = class_exists('Imagick') && in_array('AVIF', \Imagick::queryFormats('AVIF'), true);
        $canAvif = $canAvifGd || $canAvifImagick;

        if (! $canWebp && ! $canAvif) {
            Log::warning('Image variants skipped: missing WebP/AVIF encoder support.', [
                'path' => $clean,
                'gd_loaded' => extension_loaded('gd'),
                'webp' => $canWebp,
                'avif_gd' => $canAvifGd,
                'avif_imagick' => $canAvifImagick,
            ]);

            return;
        }

        $sourcePath = $disk->path($clean);
        [$width, $height] = @getimagesize($sourcePath) ?: [null, null];
        if (! $width || ! $height) {
            Log::warning('Image variants skipped: unable to read image dimensions.', [
                'path' => $clean,
                'source' => $sourcePath,
            ]);

            return;
        }

        $variantDir = $disk->path('variants');
        if (! is_dir($variantDir)) {
            if (! mkdir($variantDir, 0775, true) && ! is_dir($variantDir)) {
                Log::warning('Image variants failed: unable to create variants directory.', [
                    'path' => $variantDir,
                ]);

                return;
            }
        }

        $base = pathinfo($clean, PATHINFO_FILENAME);
        $entriesWebp = [];
        $entriesAvif = [];

        foreach ($widths as $targetWidth) {
            if ($targetWidth > $width) {
                continue;
            }

            $targetNameWebp = $base.'-'.$targetWidth.'.webp';
            $targetRelWebp = 'variants/'.$targetNameWebp;
            $targetPathWebp = $disk->path($targetRelWebp);

            $targetNameAvif = $base.'-'.$targetWidth.'.avif';
            $targetRelAvif = 'variants/'.$targetNameAvif;
            $targetPathAvif = $disk->path($targetRelAvif);

            if ((! $canWebp || file_exists($targetPathWebp)) && (! $canAvif || file_exists($targetPathAvif))) {
                if ($canWebp) {
                    $entriesWebp[$targetWidth] = '/storage/'.$targetRelWebp;
                }
                if ($canAvif) {
                    $entriesAvif[$targetWidth] = '/storage/'.$targetRelAvif;
                }

                continue;
            }

            $newHeight = (int) round($height * ($targetWidth / $width));
            $image = self::createImageResource($sourcePath, $ext);
            if (! $image) {
                Log::warning('Image variants skipped: failed to read source image.', [
                    'path' => $clean,
                    'source' => $sourcePath,
                ]);

                continue;
            }

            $canvas = imagecreatetruecolor($targetWidth, $newHeight);
            if ($ext === 'png') {
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                imagefill($canvas, 0, 0, $transparent);
            }

            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $targetWidth, $newHeight, $width, $height);

            if ($canWebp && ! file_exists($targetPathWebp)) {
                $written = imagewebp($canvas, $targetPathWebp, 70);
                if (! $written) {
                    Log::warning('Image variants failed: WebP write failed.', [
                        'path' => $clean,
                        'target' => $targetPathWebp,
                    ]);
                }
            }

            if ($canAvif && ! file_exists($targetPathAvif)) {
                $written = false;
                if ($canAvifGd) {
                    $written = imageavif($canvas, $targetPathAvif, 50);
                } elseif ($canAvifImagick) {
                    $written = self::writeAvifWithImagick($sourcePath, $targetPathAvif, $targetWidth, $newHeight);
                }

                if (! $written) {
                    Log::warning('Image variants failed: AVIF write failed.', [
                        'path' => $clean,
                        'target' => $targetPathAvif,
                        'via' => $canAvifGd ? 'gd' : ($canAvifImagick ? 'imagick' : 'none'),
                    ]);
                }
            }

            if ($canWebp) {
                $entriesWebp[$targetWidth] = '/storage/'.$targetRelWebp;
            }
            if ($canAvif) {
                $entriesAvif[$targetWidth] = '/storage/'.$targetRelAvif;
            }

            imagedestroy($image);
            imagedestroy($canvas);
        }

        if ($entriesWebp || $entriesAvif) {
            self::updateManifest($clean, [
                'webp' => $entriesWebp,
                'avif' => $entriesAvif,
            ]);
        }
    }

    private static function extractPaths(string|array|null $state): array
    {
        if (! $state) {
            return [];
        }

        if (is_string($state)) {
            return [$state];
        }

        $paths = [];
        foreach ($state as $value) {
            if (is_array($value)) {
                $paths = array_merge($paths, self::extractPaths($value));
            } elseif (is_string($value)) {
                $paths[] = $value;
            }
        }

        return $paths;
    }

    private static function createImageResource(string $path, string $ext)
    {
        return match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path) ?: null,
            'png' => @imagecreatefrompng($path) ?: null,
            default => null,
        };
    }

    private static function writeAvifWithImagick(string $sourcePath, string $targetPath, int $targetWidth, int $targetHeight): bool
    {
        try {
            $imagick = new \Imagick($sourcePath);
            $imagick->setImageFormat('avif');
            $imagick->setImageCompressionQuality(50);
            $imagick->resizeImage($targetWidth, $targetHeight, \Imagick::FILTER_LANCZOS, 1);
            $written = $imagick->writeImage($targetPath);
            $imagick->clear();
            $imagick->destroy();

            return (bool) $written;
        } catch (\Throwable $e) {
            Log::warning('Image variants failed: Imagick AVIF exception.', [
                'source' => $sourcePath,
                'target' => $targetPath,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private static function updateManifest(string $storagePath, array $entries): void
    {
        $manifestPath = public_path('images/variants/manifest.json');
        $manifestDir = dirname($manifestPath);

        if (! is_dir($manifestDir)) {
            mkdir($manifestDir, 0775, true);
        }

        $manifest = ['version' => 2, 'images' => []];
        if (is_file($manifestPath)) {
            $data = json_decode(file_get_contents($manifestPath), true);
            if (is_array($data)) {
                $manifest = array_merge($manifest, $data);
                $manifest['images'] = $manifest['images'] ?? [];
            }
        }

        $key = ltrim(Storage::disk('public')->url($storagePath), '/');
        $existing = $manifest['images'][$key] ?? [];
        $merged = self::mergeFormatEntries($existing, $entries);
        $manifest['images'][$key] = $merged;
        $manifest['generatedAt'] = now()->toIso8601String();

        file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private static function mergeFormatEntries(array $existing, array $incoming): array
    {
        $legacyFormats = ! isset($existing['webp']) && ! isset($existing['avif']);
        if ($legacyFormats) {
            $existing = ['webp' => $existing, 'avif' => []];
        }

        foreach (['webp', 'avif'] as $format) {
            $current = $existing[$format] ?? [];
            $next = $incoming[$format] ?? [];
            if (! is_array($current)) {
                $current = [];
            }
            if (! is_array($next)) {
                $next = [];
            }
            $current = self::normalizeFormatEntries($current, $format);
            $next = self::normalizeFormatEntries($next, $format);
            $merged = array_replace($current, $next);
            ksort($merged);
            $existing[$format] = $merged;
        }

        return $existing;
    }

    private static function normalizeFormatEntries(array $entries, string $format): array
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
