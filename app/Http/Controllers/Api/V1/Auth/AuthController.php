<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use App\Service\AuthService;
use Illuminate\Http\Request;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterUserRequest;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Log
            LoggerHelper::alert('Login Attempt Failed', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            return ResponseHelper::unauthorized('Username or Password invalid.');
        }

        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

        // Log
        LoggerHelper::info('User Successfully Logged in.', [
            'token' => substr($token, 0, 5) . '...' . substr($token, -5),
            'user' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);

        return ResponseHelper::success('Successfully Logged in.', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ])->cookie(
            'token',
            $token,
            60,
            '/',
            null,
            true,
            true
        );
    }

    public function register(RegisterUserRequest $request)
    {
        try {
            $user = $this->authService->registerUser($request->validated(), $request->file('image'));
            
        } catch (\Throwable $th) {

            return ResponseHelper::error('Registration Failed. ', null, 500);
        }

        return ResponseHelper::success('Successfully Create new Account.', $user);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return ResponseHelper::success('User data retrieved.', [
            'user' => [
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role, // Penting untuk UI di Vue
            ]
        ]);
    }
}
