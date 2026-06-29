<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LoggerHelper
{
    public static function info(string $message, array $context = []): void
    {
        $context = self::formatContext($context);
        Log::info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        $context = self::formatContext($context);
        Log::error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        $context = self::formatContext($context);
        Log::warning($message, $context);
    }

    public static function notice(string $message, array $context = []): void
    {
        $context = self::formatContext($context);
        Log::notice($message, $context);
    }

    public static function alert(string $message, array $context = []): void
    {
        $context = self::formatContext($context);
        Log::alert($message, $context);
    }

    private static function formatContext(array $context): array
    {
        $user = auth('sanctum')->user();

        return array_merge([
            'user_id' => $user?->id,
            'username' => $user?->username ?? $user?->email,
            'role' => $user?->role,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'url' => request()?->fullUrl(),
            'method' => request()?->method(),
            'datetime' => now()->format('d F Y, H:i:s'),
        ], $context);
    }
}
