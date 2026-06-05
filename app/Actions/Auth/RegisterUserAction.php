<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\Role;
use App\Helpers\LoggerHelper;
use App\Models\User;
use App\Repository\UserRepository;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RegisterUserAction
{
    public function __construct(
        private UserRepository $userRepository,
        private FileService $fileService,
    ) {}

    public function execute(array $data, ?UploadedFile $profilePicture = null): User
    {
        try {
            return DB::transaction(function () use ($data, $profilePicture) {
                $uploadedFileId = $this->fileService->handleUploadAndSave($profilePicture, 'file/profile-pictures')?->id;

                $user = $this->userRepository->save(new User([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'role' => Role::HeadOfFamily,
                ]));

                $user->headOfFamily()->create([
                    'file_id' => $uploadedFileId,
                    'full_name' => $data['full_name'],
                    'identity_number' => $data['identity_number'],
                    'gender' => $data['gender'],
                    'date_of_birth' => $data['date_of_birth'],
                    'occupation' => $data['occupation'],
                    'marital_status' => $data['marital_status'],
                    'phone_number' => $data['phone_number'] ?? null,
                ]);

                LoggerHelper::info('User registered successfully', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ]);

                return $user->load('headOfFamily.file');
            });
        } catch (\Throwable $th) {
            LoggerHelper::error('Failed to register user', [
                'error' => $th->getMessage(),
                'email' => $data['email'] ?? 'N/A',
                'username' => $data['username'] ?? 'N/A',
            ]);

            throw $th;
        }
    }
}
