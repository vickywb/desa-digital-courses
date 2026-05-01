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
        // Cek kuota staff
        $this->maxQuotaForRole($user, $newRole);

        return DB::transaction(function () use ($user, $newRole) {
            $user->update(['role' => $newRole->value]);

            LoggerHelper::info('User role updated successfully.', [
                'user_id'  => $user->id,
                'new_role' => $newRole->value,
            ]);

            return $user->fresh();
        });
    }

    // Cek kuota untuk role tertentu
    public function maxQuotaForRole(User $user, Role $newRole): void
    {
        $maxQuota = $newRole->maxQuota();

        if ($maxQuota === null) return;

        $currentQuota = User::where('role', $newRole->value)
            ->where('id', '!=', $user->id)
            ->count();
        
        if ($currentQuota >= $maxQuota) {
            throw new \Exception("Quota for role {$newRole->value} has been reached.");
        }
    }


}