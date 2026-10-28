<?php

namespace App\Services\Payment\Providers;

use App\Models\PermintaanPembayaran;
use App\Services\Payment\NormalizedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransAdapter implements ProviderAdapterInterface
{
    /** Marker used by the placeholder value shipped in .env.example. */
    private const PLACEHOLDER_KEY_MARKER = 'REPLACE_WITH';

    /**
     * Midtrans is usable only when a real server key is configured.
     * A non-empty placeholder (SB-Mid-server-REPLACE_WITH_YOUR_KEY) is
     * not a credential — treat it as "not configured" so the webhook
     * answers 503 instead of failing signature verification with a 401.
     */
    public function isEnabled(): bool
    {
        $serverKey = (string) config('midtrans.server_key', '');

        return $serverKey !== ''
            && !str_contains($serverKey, self::PLACEHOLDER_KEY_MARKER);
    }

    /**
     * Verify Midtrans signature_key (§10.4):
     * SHA512(order_id + status_code + gross_amount + ServerKey)
     */
    public function authenticate(Request $request): bool
    {
        $data = $request->all();

        $orderId     = $data['order_id'] ?? '';
        $statusCode  = $data['status_code'] ?? '';
        $grossAmount = $data['gross_amount'] ?? '';
        $serverKey   = config('midtrans.server_key', '');

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        $received = $data['signature_key'] ?? '';

        // Constant-time comparison to prevent timing attacks
        return hash_equals($expected, $received);
    }

    /**
     * Map Midtrans status to normalized status (§10.4).
     */
    private function mapStatus(string $transactionStatus, string $fraudStatus = ''): string
    {
        return match ($transactionStatus) {
            'settlement' => NormalizedNotification::STATUS_SUCCESS,
            // Card payments only — not used for VA. A capture still depends on
            // the fraud check: "challenge" waits for manual review.
            'capture'    => match ($fraudStatus) {
                'accept' => NormalizedNotification::STATUS_SUCCESS,
                'deny'   => NormalizedNotification::STATUS_DENIED,
                default  => NormalizedNotification::STATUS_PENDING,
            },
            'pending'    => NormalizedNotification::STATUS_PENDING,
            'expire'     => NormalizedNotification::STATUS_EXPIRED,
            'cancel'     => NormalizedNotification::STATUS_CANCELLED,
            'deny'       => NormalizedNotification::STATUS_DENIED,
            'failure'    => NormalizedNotification::STATUS_FAILED,
            default      => NormalizedNotification::STATUS_UNKNOWN,
        };
    }

    public function normalize(Request $request): NormalizedNotification
    {
        $data = $request->all();

        $orderId       = $data['order_id'] ?? '';
        $transactionId = $data['transaction_id'] ?? '';
        $status        = $this->mapStatus(
            $data['transaction_status'] ?? '',
            $data['fraud_status'] ?? ''
        );

        // Resolve school VA from the payment attempt
        $attempt  = PermintaanPembayaran::where('order_id', $orderId)->first();
        $schoolVa = $attempt?->tagihan?->siswa?->virtualAccount?->nomor_va ?? '';

        // Extract provider-issued VA (BR-15)
        $providerVa = null;
        if (!empty($data['va_numbers'][0]['va_number'])) {
            $providerVa = $data['va_numbers'][0]['va_number'];
        } elseif (!empty($data['permata_va_number'])) {
            $providerVa = $data['permata_va_number'];
        }

        // Amount: Midtrans sends as string like "350000.00"
        $amount = (int) round((float) ($data['gross_amount'] ?? 0));

        // Paid at: use settlement_time, fallback to transaction_time
        $paidAt = $data['settlement_time'] ?? $data['transaction_time'] ?? now()->toDateTimeString();

        return new NormalizedNotification(
            provider:      'midtrans',
            orderRef:      $orderId,
            transactionId: $transactionId,
            schoolVa:      $schoolVa,
            providerVa:    $providerVa,
            amount:        $amount,
            status:        $status,
            paidAt:        $paidAt,
            raw:           $data,
        );
    }
}
