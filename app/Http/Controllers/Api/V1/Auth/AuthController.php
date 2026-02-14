<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Service\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        // Determine login field
        $loginField = $request->has('email') ? 'email' : 'username';
        $loginValue = $request->has('email') ? $request->email : $request->username;
        
        // Attempt login
        if (!Auth::attempt([
            $loginField => $loginValue,
            'password' => $request->password
        ])) {
            // Log failed attempt
            LoggerHelper::alert('Login Attempt Failed', [
                $loginField => $loginValue,
                'ip' => $request->ip(),
            ]);
            
            return ResponseHelper::error('Invalid credentials.', null, 401);
        }
        
        // Get authenticated user
        $user = Auth::user();
        
        // Generate token
        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;
        
        // Log success
        LoggerHelper::info('User Successfully Logged In.', [
            'token' => substr($token, 0, 5) . '...' . substr($token, -5),
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],
        ]);
        
        // Load relationships
        $user->load('headOfFamily.file');
        
        // Return JSON response
        return ResponseHelper::success('Successfully Logged In.', [
            'user' => $user,
            'token' => $token,
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

        return ResponseHelper::success('Successfully Create new Account.', $user, 201);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('headOfFamily.file');

        return ResponseHelper::success('User data retrieved.', [
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        // // Delete current access token only
        // $request->user()->currentAccessToken()?->delete();

        $user = $request->user();
        
        $user->tokens()->delete();
            
        return ResponseHelper::success('Successfully logged out.', null);
    }
}
