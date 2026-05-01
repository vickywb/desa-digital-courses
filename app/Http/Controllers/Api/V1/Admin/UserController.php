<?php

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
    )
    {}

    public function index()
    {
        $users = $this->userRepository->get([
            'with' => ['headOfFamily']
        ]);

        return ResponseHelper::success('Users retrived successfully', new UserCollection($users), 200);
    }

    public function show(string $id)
    {
        //
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            $updatedUser = $this->userService->updateUserRole(
                $user,
                Role::from($request->validated('role'))
            );

            LoggerHelper::info('User role updated successfully.', [
                'user_id'  => $user->id,
                'new_role' => $updatedUser->role->value,
            ]);

            return ResponseHelper::success(
                'User updated successfully',
                new UserResource($updatedUser),
                200
            );

        } catch (\Throwable $e) {
            
            LoggerHelper::error('Failed to update user role.', [
                'error'   => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return ResponseHelper::error('Failed to update user role.', null, 500);
        }
    }
}
