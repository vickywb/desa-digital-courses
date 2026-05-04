<?php

namespace App\Services;

use App\Helpers\FileHelpers;
use App\Helpers\LoggerHelper;
use App\Models\File;
use App\Repository\FileRepository;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function __construct(private FileRepository $fileRepository){}

    // Upload file to storage
    public function uploadFileToStorage(?UploadedFile $file, string $directory): ?array
    {
        if (!$file) return null;

        return FileHelpers::uploadFile($file, $directory);
    }

    // Save file to Database
    public function saveFileToDatabase(array $fileInfo): File
    {
        return $this->fileRepository->create($fileInfo);
    }

    // Delete file from storage
    public function deleteFileFromStorage(string $filePath): void
    {
        FileHelpers::delete($filePath);
    }

    // Function untuk menghandle upload dan store data ke database, jika gagal file akan dihapus di storage
    public function handleUploadAndSave(?UploadedFile $file, string $directory): ?File
    {
        if (!$file) return null;

        $fileInfo = $this->uploadFileToStorage($file, $directory);
        try {
            $savedFile = $this->saveFileToDatabase($fileInfo);

            LoggerHelper::info('Success Upload File.');

            return $savedFile;

        } catch (\Throwable $th) {

            $this->deleteFileFromStorage($fileInfo['file_path']);

            LoggerHelper::alert('Failed to save file to DB, storage cleaned up.');

            throw $th;
        }
    }

    // Delete File on databse and storage
    public function deleteFile(int $fileId): bool
    {
        $file = $this->fileRepository->findById($fileId);
        if (!$file) return false;

        try {
            FileHelpers::delete($file->file_path); 
            
            return $this->fileRepository->delete($file); 
        } catch (\Throwable $th) {

            LoggerHelper::error("Failed to delete file with ID {$fileId}: " . $th->getMessage());
            throw $th;
        }
    }
}