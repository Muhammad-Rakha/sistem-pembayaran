<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\NormalizedNotification;
use Illuminate\Http\Request;

class SimulatorAdapter implements ProviderAdapterInterface
{
    public function isEnabled(): bool
    {
        // Automatically disabled in production (§10.5, FR-33)
        if (config('midtrans.is_production', false)) {
            return false;
        }
        return (bool) config('midtrans.simulator_enabled', true);
    }

    /**
     * Verify HMAC-SHA256 signature in the X-Simulator-Signature header.
     * Signature = HMAC-SHA256(rawBody, SIMULATOR_WEBHOOK_SECRET).
     */
    public function authenticate(Request $request): bool
    {
        $secret    = config('midtrans.simulator_secret', '');
        $rawBody   = $request->getContent();
        $expected  = hash_hmac('sha256', $rawBody, $secret);
        $received  = $request->header('X-Simulator-Signature', '');

        return hash_equals($expected, $received);
    }

    /**
     * Parse the source callback payload (§10.5):
     * {
     *   "virtual_account": "8800012345",
     *   "amount": 350000,
     *   "transaction_id": "TRX-20260918-001",
     *   "status": "PAID",
     *   "paid_at": "2026-09-25 10:15:00"
     * }
     */
    public function normalize(Request $request): NormalizedNotification
    {
        $data = $request->all();

        $status = strtoupper($data['status'] ?? '') === 'PAID'
            ? NormalizedNotification::STATUS_SUCCESS
            : NormalizedNotification::STATUS_FAILED;

        $schoolVa  = $data['virtual_account'] ?? '';
        $amount    = (int) ($data['amount'] ?? 0);
        $txId      = $data['transaction_id'] ?? '';
        $paidAt    = $data['paid_at'] ?? now()->toDateTimeString();

        // For the simulator, order_ref is the transaction_id itself
        // (or an explicit order_id if provided by the admin console)
        $orderRef = $data['order_id'] ?? $txId;

        return new NormalizedNotification(
            provider:      'simulator',
            orderRef:      $orderRef,
            transactionId: $txId,
            schoolVa:      $schoolVa,
            providerVa:    null,
            amount:        $amount,
            status:        $status,
            paidAt:        $paidAt,
            raw:           $data,
        );
    }
}
