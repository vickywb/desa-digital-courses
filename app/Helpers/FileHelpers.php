<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image; // Jika pakai v3
use Illuminate\Support\Str;

class FileHelpers
{
    public static function uploadFileToStorage(UploadedFile $file, string $directory): array
    {
        // Tentukan nama file baru dengan ekstensi .webp
        $filename = Str::random(20) . '.webp';
        $path = $directory . '/' . $filename;

        // Proses konversi menggunakan Intervention Image
        // baca file asli, lalu encode ke webp dengan kualitas 80
        $encodedImage = Image::read($file)->toWebp(80);

        // Simpan hasil konversi ke disk public
        Storage::disk('public')->put($path, (string) $encodedImage);

        // Ambil data untuk tabel 'files'
        return [
            'file_name' => $file->getClientOriginalName(), // Nama asli untuk referensi user
            'file_url'  => asset('storage/' . $path),
            'file_path' => $path,
            'file_type' => 'image/webp', // Sekarang tipenya sudah webp
            'file_size' => strlen((string) $encodedImage), // Ukuran setelah dikompresi
        ];
    }
}