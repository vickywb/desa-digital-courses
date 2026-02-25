<?php

namespace App\Services;

use App\Helpers\FileHelpers;
use App\Repository\FileRepository;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function __construct(
        private FileRepository $fileRepository
    ) {}

    /**
     * Upload file and save to database.
     *
     * @param  UploadedFile|null  $file
     * @param  string  $directory
     * @return array|null  ['id' => int, 'path' => string]
     */
    public function uploadFile(?UploadedFile $file, string $directory): ?array
    {
        if (!$file) {
            return null;
        }

        // Upload to storage
        $fileInfo = FileHelpers::uploadFile($file, $directory);

        // Save to database
        $savedFile = $this->fileRepository->create($fileInfo);

        // Return with ID and path
        return [
            'id'   => $savedFile->id,
            'path' => $fileInfo['file_path'],
        ];
    }

    /**
     * Delete file by ID (storage + database).
     *
     * @param  int  $fileId
     * @return bool
     */
    public function deleteFile(int $fileId): bool
    {
        $file = $this->fileRepository->findById($fileId);

        if (!$file) {
            return false;
        }

        // Delete physical file
        FileHelpers::delete($file->file_path);

        // Delete database record
        return $this->fileRepository->delete($file);
    }

    /**
     * Delete file by path (for cleanup on error).
     *
     * @param  string  $filePath
     * @return bool
     */
    public function deleteFileByPath(string $filePath): bool
    {
        return FileHelpers::delete($filePath);
    }
}