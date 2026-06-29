<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\Role;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repository\UserRepository;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private UserRepository $userRepository
    ) {}

    public function index()
    {
        $users = $this->userRepository->get([
            'with' => ['headOfFamily'],
        ], 20);

        return ResponseHelper::success('Users retrieved successfully', new UserCollection($users), 200);
    }

    public function show(string $id)
    {
        $user = $this->userRepository->get([
            'with' => ['headOfFamily'],
            'id' => $id,
        ])->first();

        if (! $user) {
            return ResponseHelper::error('User not found.', null, 404);
        }

        return ResponseHelper::success('User retrieved successfully', new UserResource($user), 200);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            $updatedUser = $this->userService->updateUserRole(
                $user,
                Role::from($request->validated('role'))
            );

            if ($request->has('is_active')) {
                $updatedUser->update(['is_active' => $request->boolean('is_active')]);
                $updatedUser = $updatedUser->fresh();
            }

            LoggerHelper::info('User updated successfully.', [
                'user_id' => $user->id,
                'new_role' => $updatedUser->role->value,
                'is_active' => $updatedUser->is_active,
            ]);

            return ResponseHelper::success(
                'User updated successfully',
                new UserResource($updatedUser),
                200
            );

        } catch (\Throwable $e) {

            LoggerHelper::error('Failed to update user.', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return ResponseHelper::error('Failed to update user.', null, 500);
        }
    }
}
