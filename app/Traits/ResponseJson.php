<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait ResponseJson {

    protected function successResponse(string $message, array $data = [], int $code = 200): JsonResponse {
        return response()->json(
            array_merge([
                'code' => $code,
                'msg' => 'success',
                'message' => $message,
            ], $data ? ['data' => $data] : []),
            $code
        );
    }

    protected function errorResponse(string $message, array $errors = [], int $code = null): JsonResponse {
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
