<?php
namespace App\Http\Middleware;

use App\Helpers\LoggerHelper;
use Closure;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();
        
        // Cek user, apakah user terautentikasi dan role diizinkan
        if (!$user || !in_array($user->role, $roles)) {

            // Log
            LoggerHelper::error('Access Denied', [
                'user_id' => $user->id,
                'roles' => $user->role ?? 'head_family'
            ]);

            return ResponseHelper::forbidden('Access Denied: Insufficient permissions.', 403);
        }

        return $next($request);
    }
}