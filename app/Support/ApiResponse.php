<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponse
{
    /**
     * Return a standardized success JSON response.
     */
    public static function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $message,
            'meta'    => static::meta(),
        ], $status);
    }

    /**
     * Return a standardized error JSON response.
     */
    public static function error(string $message = 'An error occurred', int $status = 500, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data'    => null,
            'message' => $message,
            'errors'  => $errors,
            'meta'    => static::meta(),
        ], $status);
    }

    /**
     * Return a 401 Unauthorized response.
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return static::error($message, 401);
    }

    /**
     * Return a 403 Forbidden response.
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return static::error($message, 403);
    }

    /**
     * Return a 404 Not Found response.
     */
    public static function notFound(string $message = 'Not found'): JsonResponse
    {
        return static::error($message, 404);
    }

    /**
     * Return a 202 Accepted response (for async/queued operations).
     */
    public static function accepted(string $message = 'Request accepted, processing in background'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => $message,
            'meta'    => static::meta(),
        ], 202);
    }

    /**
     * Build standardized meta payload.
     */
    private static function meta(): array
    {
        $requestId = request()->header('X-Request-ID', null);

        return [
            'timestamp'  => now()->toIso8601String(),
            'request_id' => $requestId,
        ];
    }
}
