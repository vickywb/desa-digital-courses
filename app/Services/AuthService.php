<?php

namespace App\Services;

use App\Enums\Role;
use App\Helpers\LoggerHelper;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private FileService $fileService
    ) {}

    /**
     * Register new user with head of family profile.
     *
     * @param  array  $data  Validated user data
     * @param  UploadedFile|null  $profilePicture  Optional profile picture
     * @return User  User with loaded relations
     * @throws \Throwable
     */
    public function registerUser(array $data, ?UploadedFile $profilePicture = null): User
    {
        return DB::transaction(function () use ($data, $profilePicture) {
            $uploadedFileId = null;

            try {
                // 1. Upload profile picture (if exists)
                if ($profilePicture) {
                    $fileData = $this->fileService->uploadFile($profilePicture, 'profile-photos');
                    $uploadedFileId = $fileData['id'];
                }

                // 2. Create user account
                $user = $this->userRepository->save(new User([
                    'username' => $data['username'],
                    'email'    => $data['email'],
                    'password' => $data['password'],
                    'role'     => Role::HeadOfFamily,
                ]));

                // 3. Create head of family profile
                $user->headOfFamily()->create([
                    'file_id'         => $uploadedFileId,
                    'full_name'       => $data['full_name'],
                    'identity_number' => $data['identity_number'],
                    'gender'          => $data['gender'],
                    'date_of_birth'   => $data['date_of_birth'],
                    'occupation'      => $data['occupation'],
                    'marital_status'  => $data['marital_status'],
                    'phone_number'    => $data['phone_number'] ?? null,
                ]);

                // 4. Log success
                LoggerHelper::info('User registered successfully', [
                    'user_id'  => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                ]);

                // 5. Return user with relations
                return $user->load('headOfFamily.file');

            } catch (\Throwable $th) {
                // Cleanup uploaded file on error
                if ($uploadedFileId) {
                    $this->fileService->deleteFile($uploadedFileId);
                }

                // Log error with details
                LoggerHelper::error('Failed to register user', [
                    'error'    => $th->getMessage(),
                    'email'    => $data['email'] ?? 'N/A',
                    'username' => $data['username'] ?? 'N/A',
                ]);

                // Re-throw to trigger DB::transaction rollback
                throw $th;
            }
        });
    }
}