<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ResponseHelper
{
    /**
     * Membuat response JSON sukses dengan format standar.
     * Jika data adalah ResourceCollection (Pagination),
     * maka meta & links akan digabungkan secara otomatis.
     */
    public static function success(
        string $message,
        int $statusCode = 200,
        mixed $data = null,
        string $status = 'Success'
    ): JsonResponse 
    {
        $response = [
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ];

        // Jika data adalah ResourceCollection (Pagination), gabungkan meta & links secara otomatis
        if ($data instanceof ResourceCollection) {
            $dataArray = $data->response()->getData(true);
            $response['data']  = $dataArray['data'];
            $response['meta']  = $dataArray['meta'] ?? null;
            $response['links'] = $dataArray['links'] ?? null;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Membuat response JSON error dengan format standar.
     */
    public static function error(
        string $message,
        int $statusCode = 400,
        mixed $data = null,
        string $status = 'Error'
    ): JsonResponse 
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }
}