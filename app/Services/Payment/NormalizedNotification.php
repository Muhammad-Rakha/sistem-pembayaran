<?php

namespace App\Services\Payment;

/**
 * Normalized notification DTO (§10.3).
 * Provider adapters convert their raw payload into this structure
 * before passing it to the shared webhook core.
 */
class NormalizedNotification
{
    public const STATUS_SUCCESS   = 'SUCCESS';
    public const STATUS_PENDING   = 'PENDING';
    public const STATUS_FAILED    = 'FAILED';
    public const STATUS_EXPIRED   = 'EXPIRED';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const STATUS_DENIED    = 'DENIED';
    public const STATUS_UNKNOWN   = 'UNKNOWN';

    public function __construct(
        public readonly string  $provider,          // 'midtrans' | 'simulator'
        public readonly string  $orderRef,          // local payment-attempt order_id
        public readonly string  $transactionId,     // provider transaction ID (BR-12)
        public readonly string  $schoolVa,          // school VA number (matched from attempt)
        public readonly ?string $providerVa,        // VA issued by provider (BR-15)
        public readonly int     $amount,            // amount in IDR (integer)
        public readonly string  $status,            // one of STATUS_* above
        public readonly string  $paidAt,            // datetime string (actual payment time)
        public readonly array   $raw,               // full original payload
    ) {}

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }
}
