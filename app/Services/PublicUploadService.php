<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PublicUploadService
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function store(UploadedFile $file, string $directory = 'articles'): string
    {
        if (! $file->isValid()) {
            throw new \InvalidArgumentException('Invalid uploaded file.');
        }

        $mimeType = (string) $file->getMimeType();
        if (! isset(self::ALLOWED_MIME_TYPES[$mimeType])) {
            throw new \InvalidArgumentException('Unsupported image type.');
        }

        if ($file->getSize() > 4096 * 1024) {
            throw new \InvalidArgumentException('Image exceeds size limit.');
        }

        $directory = $this->sanitizeDirectory($directory);
        $targetDirectory = public_path('uploads/'.$directory);

        if (! is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $extension = self::ALLOWED_MIME_TYPES[$mimeType];
        $filename = Str::uuid()->toString().'.'.$extension;
        $targetPath = $targetDirectory.DIRECTORY_SEPARATOR.$filename;

        $file->move($targetDirectory, $filename);

        if (is_file($targetPath)) {
            chmod($targetPath, 0644);
        }

        return $directory.'/'.$filename;
    }

    public function files(string $directory = 'articles'): array
    {
        $directory = $this->sanitizeDirectory($directory);
        $path = public_path('uploads/'.$directory);

        if (! is_dir($path)) {
            return [];
        }

        $files = [];
        foreach (scandir($path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $path.DIRECTORY_SEPARATOR.$entry;
            if (! is_file($fullPath)) {
                continue;
            }

            $extension = strtolower((string) pathinfo($entry, PATHINFO_EXTENSION));
            if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $files[] = $directory.'/'.$entry;
        }

        sort($files);

        return $files;
    }

    public function url(?string $path): ?string
    {
        $path = $this->normalizePath($path);
        if ($path === null) {
            return null;
        }

        if ($this->hasNewFile($path)) {
            return asset('uploads/'.$path);
        }

        if ($this->hasLegacyFile($path)) {
            return asset('storage/'.$path);
        }

        return asset('uploads/'.$path);
    }

    private function hasNewFile(string $path): bool
    {
        return is_file(public_path('uploads/'.$path));
    }

    private function hasLegacyFile(string $path): bool
    {
        return is_file(storage_path('app/public/'.$path));
    }

    private function sanitizeDirectory(string $directory): string
    {
        $segments = [];
        foreach (explode('/', str_replace('\\', '/', $directory)) as $segment) {
            $segment = basename(trim($segment));
            if ($segment === '' || $segment === '.' || $segment === '..') {
                continue;
            }
            if (! preg_match('/^[A-Za-z0-9_-]+$/', $segment)) {
                continue;
            }
            $segments[] = $segment;
        }

        return implode('/', $segments) ?: 'files';
    }

    private function normalizePath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $path = trim($path);
        if ($path === '') {
            return null;
        }

        if (str_contains($path, '://')) {
            $parsedPath = parse_url($path, PHP_URL_PATH);
            $path = is_string($parsedPath) ? $parsedPath : $path;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        $segments = [];
        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                continue;
            }
            $segments[] = $segment;
        }
        $path = implode('/', $segments);

        foreach (['uploads', 'storage', 'public'] as $prefix) {
            while (preg_match('#^'.$prefix.'/#i', $path)) {
                $path = preg_replace('#^'.$prefix.'/#i', '', $path, 1) ?? $path;
            }
        }

        return $path !== '' ? $path : null;
    }
}
