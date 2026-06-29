<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\LogoutUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\HeadOfFamilyResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private RegisterUserAction $registerUserAction,
        private LoginUserAction $loginUserAction,
        private LogoutUserAction $logoutUserAction,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $identifier = $validated['email'] ?? $validated['username'] ?? $validated['identity_number'];
            $data = $this->loginUserAction->execute($identifier, $validated['password']);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Login failed. Please check your credentials.', null, 401);
        }

        return ResponseHelper::success('Successfully logged in.', [
            'user' => new UserResource($data['user']),
            'token' => $data['token'],
        ])->cookie(
            'token',
            $data['token'],
            0,
            '/',
            null,
            true,
            true
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logoutUserAction->execute($request->user());

        return ResponseHelper::success('Successfully logged out.', null);
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $user = $this->registerUserAction->execute($request->validated(), $request->file('image'));
        } catch (\Throwable $th) {
            return ResponseHelper::error('Registration Failed.', null, 500);
        }

        return ResponseHelper::success('Successfully Create new Account.', $user, 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->me($request->user());

        return ResponseHelper::success('User data retrieved.', [
            'user' => new UserResource($user),
        ], 200);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $headOfFamily = $request->user()->headOfFamily;
        $headOfFamily->update($request->validated());
        $headOfFamily = $headOfFamily->fresh('file');

        return ResponseHelper::success('Profile updated successfully.', new HeadOfFamilyResource($headOfFamily), 200);
    }
}
