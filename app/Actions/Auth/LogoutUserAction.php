<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Helpers\LoggerHelper;
use App\Models\User;

class LogoutUserAction
{
    public function execute(User $user): bool
    {
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        LoggerHelper::info('User Successfully Logged Out.', [
            'user_id' => $user->id,
        ]);

        return true;
    }
}
