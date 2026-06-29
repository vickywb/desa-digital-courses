<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\LoggerHelper;
use App\Models\SocialAssistance;
use App\Repository\SocialAssistanceRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SocialAssistanceService
{
    public function __construct(
        private SocialAssistanceRepository $socialAssistanceRepository,
        private FileService $fileService
    ) {}

    public function createSocialAssistance(array $data, ?UploadedFile $image = null): SocialAssistance
    {
        try {
            $socialAssistance = DB::transaction(function () use ($data, $image) {
                $fileId = $image ? $this->fileService->handleUploadAndSave($image, 'file/social-assistance')?->id : null;
                $socialAssistance = $this->socialAssistanceRepository->save(new SocialAssistance([
                    ...$data,
                    'file_id' => $fileId,
                ]));

                return $socialAssistance;
            });

            LoggerHelper::info('Social assistance created successfully', [
                'social_assistance_id' => $socialAssistance->id,
                'title' => $socialAssistance->title,
            ]);

            return $socialAssistance;

        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to create social assistance',
                ['error_message' => $th->getMessage()]
            );

            throw $th;
        }
    }

    public function updateSocialAssistance(array $data, ?UploadedFile $image, SocialAssistance $socialAssistance): SocialAssistance
    {
        try {
            $socialAssistance = DB::transaction(function () use ($data, $image, $socialAssistance) {
                $oldFileId = $socialAssistance->file_id;
                $newFileId = $image
                    ? $this->fileService->handleUploadAndSave($image, 'file/social-assistance')?->id
                    : $oldFileId;

                $socialAssistance->fill([
                    ...$data,
                    'file_id' => $newFileId,
                ]);

                $socialAssistance = $this->socialAssistanceRepository->save($socialAssistance);

                return $socialAssistance;
            });

            LoggerHelper::info('Social assistance updated successfully', [
                'social_assistance_id' => $socialAssistance->id,
                'title' => $socialAssistance->title,
            ]);

            return $socialAssistance;

        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to update social assistance',
                ['error_message' => $th->getMessage()]
            );

            throw $th;
        }
    }

    public function deleteSocialAssistance(SocialAssistance $socialAssistance): void
    {
        try {
            DB::transaction(function () use ($socialAssistance) {
                $fileId = $socialAssistance->file_id;

                $socialAssistance->delete();

                if ($fileId) {
                    $this->fileService->deleteFile($fileId);
                }

                LoggerHelper::info('Social assistance deleted successfully', [
                    'social_assistance_id' => $socialAssistance->id,
                    'title' => $socialAssistance->title,
                ]);
            });
        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to delete social assistance', [
                'error_message' => $th->getMessage(),
            ]);
            throw $th;
        }
    }
}
