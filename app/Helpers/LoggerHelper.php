<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LoggerHelper
{
    // This Log used when data processing is success
    public static function info($message, array $context = [])
    {
        $context = self::formatContext($context);
        Log::info($message, $context);
    }

    // This Log used when an operation fails
    public static function error($message, array $context = [])
    {
        $context = self::formatContext($context);
        Log::error($message, $context);
    }

    // This Log used when an non-critical issues that should be reviewed
    public static function warning($message, array $context = [])
    {
        $context = self::formatContext($context);
        Log::warning($message, $context);
    }

    // This Log used when an normal but significant events
    public static function notice($message, array $context = [])
    {
        $context = self::formatContext($context);
        Log::notice($message, $context);
    }

    // This Log used when an important conditions that require immediate attention
    public static function alert($message, array $context = [])
    {
        $context = self::formatContext($context);
        Log::alert($message, $context);
    }

    // This is format data for context information to be included in each log entery
    private static function formatContext(array $context): array
    {
        $user = auth('sanctum')->user();

        return array_merge([
            'user_id' => $user?->id,
            'username' => $user?->username ?? $user?->email,
            'role' => $user?->role,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'datetime' => now()->format('d F Y, H:i:s'),
        ], $context);
    }
}
