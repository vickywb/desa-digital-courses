<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        // Cek user, apakah user terautentikasi dan role diizinkan
        if (! $user || ! $user->is_active || ! in_array($user->role?->value, $roles)) {

            // Log
            LoggerHelper::error('Access Denied', [
                'user_id' => $user?->id ?? 'guest',
                'roles' => $user?->role?->value ?? 'guest',
            ]);

            return ResponseHelper::forbidden('Access Denied: Insufficient permissions.', 403);
        }

        return $next($request);
    }
}
