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
        try {
            return DB::transaction(function () use ($data, $profilePicture) {
                $uploadedFileId = null;

                if ($profilePicture) {
                    $newFile      = $this->fileService->handleUploadAndSave($profilePicture, 'file/profile-photos');
                    $uploadedFileId = $newFile->id;
                }

                $user = $this->userRepository->save(new User([
                    'username' => $data['username'],
                    'email'    => $data['email'],
                    'password' => $data['password'],
                    'role'     => Role::HeadOfFamily,
                ]));

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

                LoggerHelper::info('User registered successfully', [
                    'user_id'  => $user->id,
                    'username' => $user->username,
                    'email'    => $user->email,
                ]);

                return $user->load('headOfFamily.file');
            });

        } catch (\Throwable $th) {
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
        $user = $this->findUserByIdentifier($identifier);
        
        if (!$user || !Hash::check($password, $user->password)) {
            LoggerHelper::warning('Login failed. Incorrect identifier or password', [
                'identifier' => $identifier,
            ]);
            throw new \Exception('Username or password is incorrect.');
        }

        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;
        
        LoggerHelper::info('User successfully logged in.', [
            'user_id' => $user->id,
            'token' => substr($token, 0, 5) . '...' . substr($token, -5),
        ]);
        
        return [
            'user' => $user->load('headOfFamily.file'),
            'token' => $token,
        ];
    }

    private function findUserByIdentifier(string $identifier): ?User
    {
        return User::where('username', $identifier)
            ->orWhereHas('headOfFamily', function ($query) use ($identifier) {
                $query->where('identity_number', $identifier);
            })->first();
    }

    public function logout(User $user): bool
    {
        $user->currentAccessToken()->delete();

        LoggerHelper::info('User Successfully Logged Out.', [
            'user_id' => $user->id,
        ]);

        return true;
    }

    public function me(User $user): User
    {
        return $user->load('headOfFamily.file');
    }
}