<?php

namespace App\Utils;

use Illuminate\Http\UploadedFile;

class Upload
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const MAX_SIZE_BYTES     = 5 * 1024 * 1024; // 5 MB

    /**
     * Handle an uploaded avatar file.
     * Returns the relative URL path on success (e.g. /uploads/avatars/avatar-xxx.jpg),
     * or throws \RuntimeException on validation failure.
     */
    public static function handleAvatar(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        self::validateFile($file);

        // Store inside public/ so Laravel's dev server can serve the files directly
        $dir = public_path('uploads/avatars');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $ext      = strtolower($file->getClientOriginalExtension());
        $filename = 'avatar-' . time() . '.' . $ext;
        $file->move($dir, $filename);

        return '/uploads/avatars/' . $filename;
    }

    /**
     * Handle an uploaded train image.
     * Returns the relative URL path on success.
     */
    public static function handleTrainImage(?UploadedFile $file): ?string
    {
        if ($file === null) {
            return null;
        }

        self::validateFile($file);

        // Store inside public/ so Laravel's dev server can serve the files directly
        $dir = public_path('uploads/trains');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $ext      = strtolower($file->getClientOriginalExtension());
        $filename = 'train-' . time() . '.' . $ext;
        $file->move($dir, $filename);

        return '/uploads/trains/' . $filename;
    }

    /**
     * Delete a file by its relative URL path (e.g. /uploads/avatars/avatar-xxx.jpg).
     */
    public static function deleteFile(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        // Try public_path first (new location), fall back to base_path (old location)
        $publicPath = public_path(ltrim($relativePath, '/'));
        if (file_exists($publicPath)) {
            unlink($publicPath);
            return;
        }

        $basePath = base_path(ltrim($relativePath, '/'));
        if (file_exists($basePath)) {
            unlink($basePath);
        }
    }

    /**
     * Validate extension and file size.
     * Throws \RuntimeException if invalid.
     */
    private static function validateFile(UploadedFile $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            throw new \RuntimeException('Only jpg, jpeg, png, webp images allowed');
        }

        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw new \RuntimeException('File size must not exceed 5 MB');
        }
    }
}