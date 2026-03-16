<?php

namespace App\Services;

use App\Enums\Role;
use App\Helpers\LoggerHelper;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        // File info sudah melakukan pengecekan dari file service
        $fileInfo = $this->fileService->uploadFileToStorage($profilePicture, 'file/profile-photos');
        $uploadedFilePath   = $fileInfo['file_path'] ?? null;

        try {
            $user = DB::transaction(function () use ($data, $fileInfo) {

                $uploadedFileId = null;

                // save file record ke DB
                if ($fileInfo) {
                    $savedFile      = $this->fileService->saveFileToDB($fileInfo);
                    $uploadedFileId = $savedFile->id;
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

                return $user->load('headOfFamily.file');
            });

            // 4. Log success setelah transaction commit
            LoggerHelper::info('User registered successfully', [
                'user_id'  => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
            ]);

            return $user;

        } catch (\Throwable $th) {
            // 5. Cleanup file jika DB gagal
            if ($uploadedFilePath) {
                $this->fileService->deleteFileFromStorage($uploadedFilePath);
            }

            LoggerHelper::error('Failed to register user', [
                'error'    => $th->getMessage(),
                'email'    => $data['email'] ?? 'N/A',
                'username' => $data['username'] ?? 'N/A',
            ]);

            throw $th;
        }
    }

    public function login(string $identifier, string $password): array
    {
        // Find user by username or identity number
        $user = User::where('username', $identifier)
            ->orWhereHas('headOfFamily', function ($query) use ($identifier) {
                $query->where('identity_number', $identifier);
            })->first();
        
        // Validate User Exists
        if (!$user) {
            LoggerHelper::warning('Login Failed. Incorrect userename or password', [
                'identifier' => $identifier,
            ]);

            throw new \Exception('Username or password is incorrect.');
        }

        // Validate Password
        if (!Hash::check($password, $user->password)) {
            LoggerHelper::warning('Login Failed. Incorrect userename or password', [
                'identifier' => $identifier,
                'user_id' => $user->id,
            ]);

            throw new \Exception('Login Failed. Username or password is incorrect.');
        }

        // Generate token
        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;
        
        // Log success
        LoggerHelper::info('User Successfully Logged In.', [
            'token' => substr($token, 0, 5) . '...' . substr($token, -5),
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
            ],
        ]);
        
        // Load relationships
        $user->load('headOfFamily.file');
        
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): bool
    {
        $user->currentAccessToken()->delete();

        LoggerHelper::info('User Successfully Logged Out.', [
            'user_id' => $user->id,
        ]);

        return true;
    }

    /**
     * Get authenticated user.
     */
    public function me(User $user): User
    {
        return $user->load('headOfFamily.file');
    }
}