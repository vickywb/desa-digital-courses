<?php

namespace App\Services;

use App\Helpers\FileHelpers;
use App\Models\File;
use App\Repository\FileRepository;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function __construct(
        private FileRepository $fileRepository
    ) {}

    /**
     * Upload file and return file info (without saving to DB). Returns null if no file provided.
     *
     * @param  UploadedFile|null  $file
     * @param  string  $directory
     * @return array|null  ['id' => int, 'path' => string]
     */
    public function uploadFileToStorage(?UploadedFile $file, string $directory): ?array
    {
        if (!$file) return null;

        return FileHelpers::uploadFile($file, $directory);
    }

    /**
     * Save file info to database and return File model.
     *
     * @param  array  $fileInfo
     * @return File
     */
    public function saveFileToDB(array $fileInfo): File
    {
        return $this->fileRepository->create($fileInfo);
    }

    // Delete file from storage (untuk cleanup jika DB gagal setelah upload)
    public function deleteFileFromStorage(string $filePath): void
    {
        FileHelpers::delete($filePath);
    }

    // Delete file record from DB and file from storage
    public function deleteFile(int $fileId): bool
    {
        $file = $this->fileRepository->findById($fileId);

        if (!$file) return false;

        FileHelpers::delete($file->file_path);

        return $this->fileRepository->delete($file);
    }
}