<?php

namespace App\Services\Payment;

class WebhookResult
{
    private function __construct(
        public readonly string $type,     // 'ok' | 'duplicate' | 'rejected' | 'auth_failed'
        public readonly string $message,
        public readonly int    $httpCode,
    ) {}

    public static function ok(string $msg = 'OK'): self
    {
        return new self('ok', $msg, 200);
    }

    public static function duplicate(): self
    {
        return new self('duplicate', 'Already processed — idempotent OK.', 200);
    }

    public static function rejected(string $msg): self
    {
        return new self('rejected', $msg, 422);
    }

    public static function authFailed(): self
    {
        return new self('auth_failed', 'Invalid signature.', 401);
    }

    public function toJsonResponse(): \Illuminate\Http\JsonResponse
    {
        $success = in_array($this->type, ['ok', 'duplicate']);
        return response()->json([
            'success' => $success,
            'type'    => $this->type,
            'message' => $this->message,
        ], $this->httpCode);
    }
}
