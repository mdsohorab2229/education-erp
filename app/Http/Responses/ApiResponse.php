<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(?string $message = null, mixed $data = null, ?array $meta = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?? __('common.success'),
            'data' => $data,
            'meta' => (object) ($meta ?? []),
            'errors' => null,
        ], $code);
    }

    protected function created(?string $message = null, mixed $data = null): JsonResponse
    {
        return $this->success($message, $data, null, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function error(string $message, int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $code);
    }

    protected function notFound(?string $message = null): JsonResponse
    {
        return $this->error($message ?? __('common.not_found'), 404);
    }

    protected function validationError(mixed $errors, ?string $message = null): JsonResponse
    {
        return $this->error($message ?? __('common.validation_failed'), 422, $errors);
    }
}
