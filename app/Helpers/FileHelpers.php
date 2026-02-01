<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class FileHelpers
{
    public static function uploadFileToStorage(UploadedFile $file, string $directory): array
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');

        // Kembalikan semua data yang dibutuhkan tabel 'files'
        return [
            'file_name' => $file->getClientOriginalName(),
            'file_url'  => asset('storage/' . $path),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ];
    }
}