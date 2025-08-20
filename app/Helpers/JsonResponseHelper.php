<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class JsonResponseHelper
{
    public static function successResponse(string $message, array $data = [], int $code = 200): JsonResponse
    {
        return response()->json(
            array_merge([
                'code' => $code,
                'msg' => 'success',
                'message' => $message,
            ], $data ? ['data' => $data] : []),
            $code
        );
    }

    public static function createdResponse(string $message, array $data = [], int $code = 201): JsonResponse
    {
        return response()->json(
            array_merge([
                'code' => $code,
                'msg' => 'created',
                'message' => $message,
            ], $data ? ['data' => $data] : []),
            $code
        );
    }

    public static function errorResponse(string $message, array $errors = [], int $code = 500): JsonResponse
    {
        Log::error($message, $errors);

        return response()->json(
            array_merge([
                'code' => $code,
                'msg' => 'error',
                'message' => $message,
            ], $errors ? ['errors' => $errors] : []),
            $code
        );
    }
}
