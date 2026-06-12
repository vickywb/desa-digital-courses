<?php

namespace App\Services;

use App\Helpers\LoggerHelper;
use App\Models\HeadOfFamily;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use App\Repository\SocialAssistanceRecipientRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class SocialAssistanceRecipientService
{
    public function __construct(
        private SocialAssistanceRecipientRepository $socialAssistanceRecipientRepository,
        private FileService $fileService
    ) {}

    public function createRecipient(array $data, SocialAssistance $socialAssistance, HeadOfFamily|string $headOfFamilyId, ?UploadedFile $proof = null): SocialAssistanceRecipient
    {
        try {
            $recipient = DB::transaction(function () use ($data, $socialAssistance, $headOfFamilyId, $proof) {
                $fileId = $proof
                    ? $this->fileService->handleUploadAndSave($proof, 'file/social-assistance-proof')?->id
                    : null;

                $recipient = $this->socialAssistanceRecipientRepository->save(new SocialAssistanceRecipient([
                    'social_assistance_id' => $socialAssistance->id,
                    'head_of_family_id' => $headOfFamilyId,
                    'file_id' => $fileId,
                    'bank' => $data['bank'],
                    'amount' => $data['amount'],
                    'account_number' => $data['account_number'],
                    'reason' => $data['reason'],
                    'status' => 'pending',
                ]));

                return $recipient;
            });

            LoggerHelper::info('Social assistance recipient created successfully', [
                'recipient_id' => $recipient->id,
                'social_assistance_id' => $socialAssistance->id,
            ]);

            return $recipient->fresh(['socialAssistance.file', 'headOfFamily.file', 'file']);

        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to create social assistance recipient', [
                'error_message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }

    public function deleteRecipient(SocialAssistanceRecipient $recipient): void
    {
        try {
            DB::transaction(function () use ($recipient) {
                $fileId = $recipient->file_id;

                $recipient->delete();

                if ($fileId) {
                    $this->fileService->deleteFile($fileId);
                }

                LoggerHelper::info('Social assistance recipient deleted successfully', [
                    'recipient_id' => $recipient->id,
                ]);
            });
        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to delete social assistance recipient', [
                'error_message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }
}
