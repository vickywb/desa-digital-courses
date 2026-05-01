<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();
            $data = $this->authService->login($validated['identifier'], $validated['password']);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Login failed. Please check your credentials.', null, 401);
        }
        
        return ResponseHelper::success('Successfully logged in.', [
            'user' => new UserResource($data['user']),
            'token' => $data['token']
        ])->cookie(
            'token',
            $data['token'],
            60,
            '/',
            null,
            true,
            true
        );
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
            
        return ResponseHelper::success('Successfully logged out.', null);
    }

    public function register(RegisterUserRequest $request)
    {
        try {
            $user = $this->authService->registerUser($request->validated(), $request->file('image'));
            
        } catch (\Throwable $th) {

            return ResponseHelper::error('Registration Failed.', null, 500);
        }

        return ResponseHelper::success('Successfully Create new Account.', $user, 201);
    }

    public function me(Request $request)
    {
       $user = $this->authService->me($request->user());

        return ResponseHelper::success('User data retrieved.', [
            'user' => new UserResource($user)
        ], 200);
    }
}
