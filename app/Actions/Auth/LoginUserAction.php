<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Helpers\LoggerHelper;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginUserAction
{
    public function execute(string $identifier, string $password): array
    {
        $user = $this->findUserByIdentifier($identifier);

        if (! $user || ! $user->is_active || ! Hash::check($password, $user->password)) {
            LoggerHelper::warning('Login failed. Incorrect credentials.');
            throw new \Exception('Username or password is incorrect.');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        LoggerHelper::info('User successfully logged in.', [
            'user_id' => $user->id,
        ]);

        return [
            'user' => $user->load('headOfFamily.file'),
            'token' => $token,
        ];
    }

    private function findUserByIdentifier(string $identifier): ?User
    {
        $user = User::where('email', $identifier)->first();

        if (! $user) {
            $user = User::where('username', $identifier)->first();
        }

        if (! $user) {
            $user = User::whereHas('headOfFamily', function ($query) use ($identifier) {
                $query->where('identity_number', $identifier);
            })->first();
        }

        return $user;
    }
}
