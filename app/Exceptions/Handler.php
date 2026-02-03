<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        // Validation (422)
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Unauthenticated (401)
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
        });

        // Not found (404) - model not found or route not found
        $this->renderable(function (ModelNotFoundException|NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found',
                ], 404);
            }
        });

        // Wrong HTTP method (405)
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed',
                ], 405);
            }
        });

        // Fallback: unexpected server errors (500)
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                // In production you usually hide the details
                $payload = [
                    'success' => false,
                    'message' => 'Server error',
                ];

                // Optional: include debug info only in local
                if (config('app.debug')) {
                    $payload['error'] = $e->getMessage();
                    $payload['exception'] = class_basename($e);
                }

                return response()->json($payload, 500);
            }
        });
    }
}
