<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Standardized API response helper.
     */
    public function apiResponse(
        mixed $data = null,
        string $status = 'success',
        mixed $error = null,
        int $code = Response::HTTP_OK,
        string $message = ''
    ): JsonResponse {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'error' => $error,
        ], $code);
    }
}
