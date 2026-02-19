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
     * Upload file dan save ke database.
     *
     * @param  UploadedFile|null  $file
     * @param  string  $directory  (e.g., 'profile-photos', 'events', 'documents')
     * @return int|null  File ID
     */
    public function upload(?UploadedFile $file, string $directory): ?int
    {
        if (!$file) {
            return null;
        }

        // 1. Upload file
        $fileInfo = FileHelpers::uploadFile($file, $directory);

        // 2. Save to database
        $savedFile = $this->fileRepository->save($fileInfo);

        // 3. Return File ID
        return $savedFile->id;
    }

    /**
     * Delete file (storage + database).
     *
     * @param  int  $fileId
     * @return bool
     */
    public function delete(int $fileId): bool
    {
        $file = $this->fileRepository->findById($fileId);

        if (!$file) {
            return false;
        }

        // 1. Delete physical file
        FileHelpers::delete($file->file_path);

        // 2. Delete from database
        return $this->fileRepository->delete($file);
    }

    /**
     * Replace file (delete old, upload new).
     *
     * @param  int|null  $oldFileId
     * @param  UploadedFile  $newFile
     * @param  string  $directory
     * @return int  New file ID
     */
    public function replace(?int $oldFileId, UploadedFile $newFile, string $directory): int
    {
        // Delete old file if exists
        if ($oldFileId) {
            $this->delete($oldFileId);
        }

        // Upload new file
        return $this->upload($newFile, $directory);
    }
}