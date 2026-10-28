<?php

namespace App\Services\Payment;

use App\Models\PermintaanPembayaran;
use App\Models\TagihanSpp;
use App\Models\Siswa;
use Midtrans\Config as MidtransConfig;
use Midtrans\CoreApi;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$isProduction = config('midtrans.is_production', false);
        MidtransConfig::$isSanitized  = config('midtrans.is_sanitized', true);
        MidtransConfig::$is3ds        = config('midtrans.is_3ds', true);
    }

    /**
     * Create a Midtrans Core API bank_transfer transaction.
     * Returns the PermintaanPembayaran record on success.
     *
     * @throws \Exception on API failure
     */
    public function createTransaction(TagihanSpp $tagihan, Siswa $siswa, string $bank): PermintaanPembayaran
    {
        // Cancel any existing waiting attempt for the same invoice+bank
        PermintaanPembayaran::where('id_tagihan', $tagihan->id_tagihan)
            ->where('bank', $bank)
            ->where('status', 'waiting')
            ->where('provider', 'midtrans')
            ->update(['status' => 'cancelled']);

        // Build unique order_id
        $orderId = 'SPP-' . $tagihan->id_tagihan . '-' . $siswa->nis . '-' . now()->format('YmdHis');

        $bankTransfer = [
            'bank' => $bank,
        ];

        // Request custom VA suffix = NIS (best-effort per bank rules)
        $customVa = $this->buildCustomVa($bank, $siswa->nis);
        if (!is_null($customVa)) {
            $bankTransfer['va_number'] = $customVa;
        }

        // Notification URL — must be a public HTTPS URL reachable by Midtrans.
        // In local dev: set MIDTRANS_NOTIFICATION_URL to your ngrok/cloudflared URL.
        // e.g. MIDTRANS_NOTIFICATION_URL=https://xxxx.ngrok.io/webhooks/midtrans
        $notificationUrl = config('midtrans.notification_url')
            ?: url('/webhooks/midtrans');

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $tagihan->nominal,
            ],
            'customer_details' => [
                'first_name' => $siswa->nama,
                'email'      => $siswa->user->username . '@smkbinabangsa.sch.id',
                'phone'      => $siswa->no_hp ?? '08000000000',
            ],
            'payment_type'   => 'bank_transfer',
            'bank_transfer'  => $bankTransfer,
            'item_details'   => [[
                'id'       => 'SPP-' . $tagihan->bulan . '-' . $tagihan->tahun,
                'price'    => (int) $tagihan->nominal,
                'quantity' => 1,
                'name'     => 'SPP ' . \Carbon\Carbon::create()->month($tagihan->bulan)->format('F') . ' ' . $tagihan->tahun,
            ]],
            // Per-transaction notification URL (overrides dashboard setting for this transaction)
            'custom_expiry'  => [
                'order_time'      => now()->format('Y-m-d H:i:s O'),
                'expiry_duration' => 1,
                'unit'            => 'day',
            ],
        ];

        // Inject notification_url into the charge call via custom field
        // Midtrans Core API accepts this in the top-level payload
        if ($notificationUrl && !str_contains($notificationUrl, 'localhost')) {
            $params['notification_url'] = $notificationUrl;
        }

        Log::info('Midtrans createTransaction request', ['order_id' => $orderId]);

        $response    = CoreApi::charge($params);
        $responseArr = json_decode(json_encode($response), true);

        Log::info('Midtrans createTransaction response', ['order_id' => $orderId, 'status_code' => $responseArr['status_code'] ?? '']);

        // Extract provider-issued VA (BR-15)
        $providerVa = null;
        if (!empty($responseArr['va_numbers'][0]['va_number'])) {
            $providerVa = $responseArr['va_numbers'][0]['va_number'];
        } elseif (!empty($responseArr['permata_va_number'])) {
            $providerVa = $responseArr['permata_va_number'];
        }

        // Expiry from response or default 1 day
        $expiry = now()->addDay();
        if (!empty($responseArr['expiry_time'])) {
            try { $expiry = \Carbon\Carbon::parse($responseArr['expiry_time']); } catch (\Exception $e) {}
        }

        $attempt = PermintaanPembayaran::create([
            'provider'          => 'midtrans',
            'order_id'          => $orderId,
            'id_tagihan'        => $tagihan->id_tagihan,
            'id_siswa'          => $siswa->id_siswa,
            'bank'              => $bank,
            'nomor_va_provider' => $providerVa,
            'nominal'           => (int) $tagihan->nominal,
            'status'            => 'waiting',
            'waktu_kedaluwarsa' => $expiry,
            'raw_request'       => $params,
            'raw_response'      => $responseArr,
        ]);

        return $attempt;
    }

    /**
     * Server-side status check (§10.4).
     * Returns the normalized status string or throws on API error.
     */
    public function getStatus(string $orderId): array
    {
        $status = Transaction::status($orderId);
        return json_decode(json_encode($status), true);
    }

    /**
     * Build a custom VA number. Rules differ per bank — this is best-effort.
     * Midtrans may override the custom VA based on bank rules.
     */
    private function buildCustomVa(string $bank, string $nis): ?string
    {
        $nis = str_pad($nis, 5, '0', STR_PAD_LEFT);
        $last5 = substr($nis, -5);
        return match (strtolower($bank)) {
            'bni'     => '88899' . $last5,   // BNI: 8-digit custom VA
            'bri'     => '26215' . $last5,   // BRI: prefix + NIS
            'permata' => null,               // Permata: random, no custom
            default   => null,
        };
    }
}
