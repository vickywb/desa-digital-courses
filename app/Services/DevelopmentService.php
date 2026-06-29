<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\LoggerHelper;
use App\Models\Development;
use App\Repository\DevelopmentRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class DevelopmentService
{
    public function __construct(
        private DevelopmentRepository $developmentRepository,
        private FileService $fileService
    ) {}

    public function createDevelopment(array $data, ?UploadedFile $image = null): Development
    {
        try {
            $development = DB::transaction(function () use ($data, $image) {
                $fileId = $image ? $this->fileService->handleUploadAndSave($image, 'file/developments')?->id : null;
                $development = $this->developmentRepository->save(new Development([
                    ...$data,
                    'file_id' => $fileId,
                ]));

                return $development;
            });

            LoggerHelper::info('Development created successfully', [
                'development_id' => $development->id,
                'title' => $development->title,
            ]);

            return $development;

        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to create development',
                ['error_message' => $th->getMessage()]
            );

            throw $th;
        }
    }

    public function updateDevelopment(array $data, ?UploadedFile $image, Development $development): Development
    {
        try {
            $development = DB::transaction(function () use ($data, $image, $development) {
                $oldFileId = $development->file_id;
                $newFileId = $image
                    ? $this->fileService->handleUploadAndSave($image, 'file/developments')?->id
                    : $oldFileId;

                $development->fill([
                    ...$data,
                    'file_id' => $newFileId,
                ]);

                $development = $this->developmentRepository->save($development);

                return $development;
            });

            LoggerHelper::info('Development updated successfully', [
                'development_id' => $development->id,
                'title' => $development->title,
            ]);

            return $development;

        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to update development',
                ['error_message' => $th->getMessage()]
            );

            throw $th;
        }
    }

    public function deleteDevelopment(Development $development)
    {
        try {
            DB::transaction(function () use ($development) {
                $fileId = $development->file_id;

                $development->delete();

                if ($fileId) {
                    $this->fileService->deleteFile($fileId);
                }

                LoggerHelper::info('Development deleted successfully', [
                    'development_id' => $development->id,
                    'title' => $development->title,
                ]);
            });
        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to delete development', [
                'error_message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }
}
