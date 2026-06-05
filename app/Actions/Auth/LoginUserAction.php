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

        if (! $user || ! Hash::check($password, $user->password)) {
            LoggerHelper::warning('Login failed. Incorrect identifier or password', [
                'identifier' => $identifier,
            ]);
            throw new \Exception('Username or password is incorrect.');
        }

        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

        LoggerHelper::info('User successfully logged in.', [
            'user_id' => $user->id,
            'token' => substr($token, 0, 5).'...'.substr($token, -5),
        ]);

        return [
            'user' => $user->load('headOfFamily.file'),
            'token' => $token,
        ];
    }

    private function findUserByIdentifier(string $identifier): ?User
    {
        return User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->orWhereHas('headOfFamily', function ($query) use ($identifier) {
                $query->where('identity_number', $identifier);
            })->first();
    }
}
