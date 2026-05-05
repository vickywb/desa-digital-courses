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
        $extension = strtolower($file->getClientOriginalExtension());
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $isImage = in_array($extension, $imageExtensions);

        try {

            if ($isImage) {
                $fileName = Str::random(20) . '.webp';
                $path = $directory . '/' . $fileName;
                $encodedImage = Image::read($file)->toWebp(80);
                $fileType = 'image/webp';
                $fileSize = strlen((string) $encodedImage);
                Storage::disk('public')->put($path, (string) $encodedImage);
            } else {
                $fileName = Str::random(20) . '.' . $extension;
                $path = $directory . '/' . $fileName;
                $fileType = $file->getClientMimeType();
                $fileSize = $file->getSize();
                Storage::disk('public')->putFileAs($directory, $file, $fileName);
            }
            // Return file info
            return [
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $fileType,
                'file_size' => $fileSize,
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