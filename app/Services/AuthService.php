<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\LogoutUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class AuthService
{
    public function __construct(
        private RegisterUserAction $registerUserAction,
        private LoginUserAction $loginUserAction,
        private LogoutUserAction $logoutUserAction,
    ) {}

    public function registerUser(array $data, ?UploadedFile $profilePicture = null): User
    {
        return $this->registerUserAction->execute($data, $profilePicture);
    }

    public function login(string $identifier, string $password): array
    {
        return $this->loginUserAction->execute($identifier, $password);
    }

    public function logout(User $user): bool
    {
        return $this->logoutUserAction->execute($user);
    }

    public function me(User $user): User
    {
        return $user->load('headOfFamily.file');
    }
}
