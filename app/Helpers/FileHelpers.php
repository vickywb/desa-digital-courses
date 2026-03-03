<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class FileHelpers
{
    /**
     * Upload file dan convert ke WebP.
     *
     * @param  UploadedFile  $file
     * @param  string  $directory  (e.g., 'profile-photos', 'events', 'documents')
     * @return array
     */
    public static function uploadFile(UploadedFile $file, string $directory): array
    {
        try {

            // Generate random string
            $filename = Str::random(20) . '.webp';
            $path = $directory . '/' . $filename;

            // Convert to WebP
            $encodedImage = Image::read($file)->toWebp(80);

            // Save to storage/app/public
            Storage::disk('public')->put($path, (string) $encodedImage);

            // Return file info
            return [
                'file_name' => $file->getClientOriginalName(),
                'file_url'  => asset('storage/' . $path),
                'file_path' => $path,
                'file_type' => 'image/webp',
                'file_size' => strlen((string) $encodedImage),
            ];

        } catch (\Throwable $th) {
            // 1. Log error agar dev tahu apa yang salah
            LoggerHelper::error('Gagal upload file: ' . $th->getMessage());

            // 2. Lempar kembali exception agar Controller tahu ada masalah
            throw new Exception('Failed to upload file. Please try again later.');
        }
    }

    /**
     * Delete file from storage.
     *
     * @param  string  $path
     * @return bool
     */
    public static function delete(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    /**
     * Get full URL dari file path.
     *
     * @param  string|null  $path
     * @return string|null
     */
    public static function url(?string $path): ?string
    {
        return $path ? asset('storage/' . $path) : null;
    }
}