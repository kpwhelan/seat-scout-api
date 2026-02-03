<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;

trait ApiResponses {
    protected function success(
        mixed $data = null,
        string $message = 'OK',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    protected function error(
        string $message = 'Error',
        int $status = 400,
        mixed $errors = null,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }
}
