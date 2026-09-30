<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected function ok(mixed $data = [], string $message = 'OK', int $status = 200): JsonResponse
    {
        $payload = ['message' => $message, 'data' => $data ?: []];

        return response()->json($payload, $status);
    }

    protected function created(mixed $data = [], string $message = 'Created'): JsonResponse
    {
        return $this->ok($data, $message, 201);
    }

    protected function noContent(string $message = 'Berhasil.'): JsonResponse
    {
        return response()->json(['message' => $message], 200);
    }

    protected function error(string $message, int $status = 422, mixed $errors = null): JsonResponse
    {
        $payload = ['message' => $message];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}