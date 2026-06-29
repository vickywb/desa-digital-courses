<?php

namespace App\Services;

use App\Enums\Role;
use App\Helpers\LoggerHelper;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function updateUserRole(User $user, Role $newRole): User
    {
        return DB::transaction(function () use ($user, $newRole) {
            $this->maxQuotaForRole($user, $newRole);

            $user->update(['role' => $newRole->value]);

            LoggerHelper::info('User role updated successfully.', [
                'user_id' => $user->id,
                'new_role' => $newRole->value,
            ]);

            return $user->fresh();
        });
    }

    public function maxQuotaForRole(User $user, Role $newRole): void
    {
        $maxQuota = $newRole->maxQuota();

        if ($maxQuota === null) {
            return;
        }

        $currentQuota = User::where('role', $newRole->value)
            ->where('id', '!=', $user->id)
            ->lockForUpdate()
            ->count();

        if ($currentQuota >= $maxQuota) {
            throw new \Exception("Quota for role {$newRole->value} has been reached.");
        }
    }
}
