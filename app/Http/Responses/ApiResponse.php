<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ApiResponse implements Responsable
{
    protected int $httpCode;
    protected string $message;
    protected mixed $data;
    protected mixed $error;

    public function __construct(
        int $httpCode,
        string $message = '',
        mixed $data = null,
        mixed $error = null
    ) {
        if ($httpCode < 200 || $httpCode > 599) {
            throw new \RuntimeException("$httpCode is not a valid HTTP status code");
        }

        $this->httpCode = $httpCode;
        $this->message = $message;
        $this->data = $data;
        $this->error = $error;
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json(
            $this->buildPayload(),
            $this->httpCode,
            options: JSON_UNESCAPED_UNICODE
        );
    }

    private function buildPayload(): array
    {
        if ($this->httpCode >= 500) {
            return [
                'success' => false,
                'code' => $this->httpCode,
                'message' => 'Internal Server Error',
            ];
        }

        if ($this->httpCode >= 400) {
            return array_filter([
                'success' => false,
                'code' => $this->httpCode,
                'message' => $this->message,
                'error' => $this->error,
            ], fn($v) => $v !== null);
        }

        return array_filter([
            'success' => true,
            'code' => $this->httpCode,
            'message' => $this->message,
            'data' => $this->data,
        ], fn($v) => $v !== null);
    }

    public static function ok(string $message = 'Ok', mixed $data = null): self
    {
        return new self(httpCode: 200, message: $message, data: $data);
    }

    public static function noContent(string $message = 'No Content'): self
    {
        return new self(httpCode: 204, message: $message);
    }

    public static function badRequest(string $message = 'Bad Request', mixed $error = null): self
    {
        return new self(httpCode: 400, message: $message, error: $error);
    }

    public static function unauthorized(string $message = 'Unauthorized', mixed $error = null): self
    {
        return new self(httpCode: 401, message: $message, error: $error);
    }

    public static function forbidden(string $message = 'Forbidden'): self
    {
        return new self(httpCode: 403, message: $message);
    }

    public static function notFound(string $message = 'Not Found', mixed $error = null): self
    {
        return new self(httpCode: 404, message: $message, error: $error);
    }

    public static function validationError(string $message = 'Validation Error', mixed $error = null): self
    {
        return new self(httpCode: 422, message: $message, error: $error);
    }

    public static function serverError(string $message = 'Internal Server Error'): self
    {
        return new self(httpCode: 500, message: $message);
    }
}
