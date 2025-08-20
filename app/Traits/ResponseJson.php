<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait ResponseJson {

    /**
     * @param string $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     *
     * Exito general 200
     */
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

    /**
     * @param string $message
     * @param array $data
     * @param int $code
     * @return JsonResponse
     *
     * Exito creado 201
     */
    protected function createdResponse(string $message, array $data = [], int $code = 201): JsonResponse {
        return response()->json(
            array_merge([
                'code' => $code,
                'msg' => 'created',
                'message' => $message,
            ], $data ? ['data' => $data] : []),
            $code
        );
    }

    /**
     * @param string $message
     * @param array $errors
     * @param int $code
     * @return JsonResponse
     *
     * Error general 500
     */
    protected function errorResponse(string $message, array $errors = [], int $code = 500): JsonResponse {
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
