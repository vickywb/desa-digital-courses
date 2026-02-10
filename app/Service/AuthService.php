<?php

namespace App\Service;

use App\Enums\Role;
use App\Models\User;
use App\Helpers\FileHelpers;
use App\Helpers\LoggerHelper;
use Illuminate\Http\UploadedFile;
use App\Repository\FileRepository;
use App\Repository\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthService
{
    private $userRepository, $fileRepository;

    public function __construct(
        UserRepository $userRepository,
        FileRepository $fileRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->fileRepository = $fileRepository;
    }

    public function getUser()
    {

    }

    public function registerUser(array $data, ?UploadedFile $profilePicture = null): User
    {
        DB::beginTransaction();
        
        try {
            $fileId = null;
            $uploadedFilePath = null;
            
            // 1. Handle File Upload
            if ($profilePicture) {
                $fileInfo = FileHelpers::uploadFileToStorage($profilePicture, 'profile-picture');
                $uploadedFilePath = $fileInfo['file_path'];
                
                $savedFile = $this->fileRepository->save($fileInfo);
                $fileId = $savedFile->id;
            }
            
            // 2. Create User
            $user = $this->userRepository->save(new User([
                'username' => $data['username'],
                'email'    => $data['email'],
                'password' => $data['password'],
                'role'     => Role::HeadOfFamily,
            ]));
            
            // 3. Create Head of Family Profile
            $user->headOfFamily()->create([
                'file_id'         => $fileId,
                'identity_number' => $data['identity_number'],
                'gender'          => $data['gender'],
                'date_of_birth'   => $data['date_of_birth'],
                'occupation'      => $data['occupation'],
                'marital_status'  => $data['marital_status'],
                'phone_number'    => $data['phone_number'] ?? null,
            ]);
            
            DB::commit();
            
            LoggerHelper::info('User registered successfully', [
                'user_id'  => $user->id,
                'username' => $user->username,
            ]);
            
            return $user->load('headOfFamily.file');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            
            if ($uploadedFilePath) {
                Storage::disk('public')->delete($uploadedFilePath);
            }
            
            LoggerHelper::error('Failed to register user', [
                'error' => $th->getMessage(),
                'email' => $data['email'] ?? 'N/A',
            ]);
            
            throw $th;
        }
    }
}