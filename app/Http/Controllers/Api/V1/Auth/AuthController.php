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
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $identifier = $credentials['identifier'];
        $password = $credentials['password'];

        try {
        $data = $this->authService->login($identifier, $password);
        
        } catch (\Throwable $th) {
           
            return ResponseHelper::error('Login Failed. Please check your credentials.', null, 401);
        }
        
        return ResponseHelper::success('Successfully Logged In.', [
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

            return ResponseHelper::error('Registration Failed. ', null, 500);
        }

        return ResponseHelper::success('Successfully Create new Account.', $user, 201);
    }

    public function me(Request $request)
    {
       $user = $this->authService->me($request->user());

        return ResponseHelper::success('User data retrieved.', [
            'user' => new UserResource($user)
        ]);
    }
}
