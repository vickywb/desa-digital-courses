<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResponseHelper
{
    /**
     * Success response dengan support untuk pagination
     */
    public static function success(
        string $message,
        array|object|null $data = null,
        int $statusCode = 200,
        string $status = 'Success'
    ): JsonResponse 
    {
        // Jika data adalah ResourceCollection (Pagination)
        if ($data instanceof ResourceCollection) {
            $dataArray = $data->response()->getData(true);
            
            return response()->json([
                'status'  => $status,
                'message' => $message,
                'data'    => $dataArray['data'],
                'meta'    => $dataArray['meta'] ?? null,
                'links'   => $dataArray['links'] ?? null,
            ], $statusCode);
        }
        
        // Response biasa (non-paginated)
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Error response dengan format standar.
     */
    public static function error(
        string $message,
        array|object|null $data = null,
        int $statusCode = 500,
        string $status = 'Error'
    ): JsonResponse 
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Response untuk validation error (shortcut).
     */
    public static function validationError(
        string $message = 'Validation failed',
        array $errors = []
    ): JsonResponse 
    {
        return self::error($message, $errors, 422, 'Validation Error');
    }

    /**
     * Response untuk unauthorized.
     */
    public static function unauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse 
    {
        return self::error($message, null, 401, 'Unauthorized');
    }

    /**
     * Response untuk forbidden.
     */
    public static function forbidden(
        string $message = 'Access Denied'
    ): JsonResponse 
    {
        return self::error($message, null, 403, 'Forbidden');
    }
}