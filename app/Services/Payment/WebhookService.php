<?php

namespace App\Services\Payment;

use App\Models\NotifikasiGateway;
use App\Models\Pembayaran;
use App\Models\PermintaanPembayaran;
use App\Models\TagihanSpp;
use App\Models\TransaksiPaymentGateway;
use App\Models\VirtualAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Shared webhook processing core (§10.2).
 *
 * Steps (called after the provider adapter has authenticated + normalized):
 *  3. Append notification log (always — BR-17)
 *  4. Idempotency check
 *  5. Match order/VA → student → invoice → amount
 *  6. Status mapping (only SUCCESS → payment)
 *  7. Record (DB transaction)
 */
class WebhookService
{
    public function process(NormalizedNotification $n): WebhookResult
    {
        // 3. Always log the notification first (BR-17, FR-30)
        $log = $this->appendLog($n, signatureValid: true);

        // 4. Idempotency: already processed this order?
        if ($this->isAlreadyProcessed($n)) {
            $this->updateLog($log, 'duplicate', 'Transaction already recorded — idempotent response.');
            Log::info("Webhook [{$n->provider}] duplicate order {$n->orderRef}");
            return WebhookResult::duplicate();
        }

        // 6. Only a confirmed SUCCESS may create a payment (BR-16)
        if (!$n->isSuccess()) {
            $this->updateLog($log, 'not-success', "Status {$n->status} — no payment created.");
            return WebhookResult::ok("Status {$n->status} recorded, no payment.");
        }

        // 5a. Resolve VA
        $va = VirtualAccount::where('nomor_va', $n->schoolVa)->where('status', 'aktif')->first();
        if (!$va) {
            $this->updateLog($log, 'rejected-unknown-va', "VA {$n->schoolVa} not found.");
            return WebhookResult::rejected("Virtual Account tidak ditemukan: {$n->schoolVa}");
        }

        $siswa = $va->siswa;
        if (!$siswa) {
            $this->updateLog($log, 'rejected-no-student', 'Siswa tidak ditemukan untuk VA ini.');
            return WebhookResult::rejected('Siswa tidak ditemukan.');
        }

        // 5b. Resolve invoice from the order (prefer payment attempt → invoice; fallback to oldest payable)
        $tagihan = null;
        $attempt = PermintaanPembayaran::where('order_id', $n->orderRef)
            ->where('id_siswa', $siswa->id_siswa)
            ->first();

        if ($attempt) {
            $tagihan = $attempt->tagihan;
        } else {
            // Simulator without order_id: pick oldest UNPAID/OVERDUE invoice
            $tagihan = TagihanSpp::where('id_siswa', $siswa->id_siswa)
                ->whereIn('status', ['UNPAID', 'OVERDUE'])
                ->whereDoesntHave('pembayaran')
                ->orderBy('tahun')->orderBy('bulan')
                ->first();
        }

        if (!$tagihan) {
            $this->updateLog($log, 'rejected-no-invoice', 'Tidak ada tagihan yang bisa dibayar.');
            return WebhookResult::rejected('Tidak ada tagihan yang bisa dibayar.');
        }

        // Invoice already PAID?
        if ($tagihan->status === 'PAID') {
            $this->updateLog($log, 'rejected-already-paid', "Tagihan {$tagihan->id_tagihan} sudah PAID.");
            return WebhookResult::rejected('Tagihan sudah lunas.');
        }

        // 5c. Amount validation
        if ($n->amount !== (int) $tagihan->nominal) {
            $msg = "Nominal tidak sesuai: diterima {$n->amount}, tagihan {$tagihan->nominal}";
            $this->updateLog($log, 'rejected-amount-mismatch', $msg);
            return WebhookResult::rejected($msg);
        }

        // 7. Record everything atomically
        try {
            $transaksi = DB::transaction(function () use ($n, $siswa, $tagihan, $attempt) {
                // Lock the invoice row to prevent concurrent double-payment
                $tagihan = TagihanSpp::lockForUpdate()->find($tagihan->id_tagihan);

                if ($tagihan->status === 'PAID') {
                    throw new \RuntimeException('race_condition_already_paid');
                }

                // Create transaction record
                $transaksi = TransaksiPaymentGateway::create([
                    'provider'          => $n->provider,
                    'order_id'          => $n->orderRef,
                    'transaction_id'    => $n->transactionId,
                    'virtual_account'   => $n->schoolVa,
                    'id_siswa'          => $siswa->id_siswa,
                    'tanggal_transaksi' => \Carbon\Carbon::parse($n->paidAt)->toDateString(),
                    'waktu_transaksi'   => \Carbon\Carbon::parse($n->paidAt)->toTimeString(),
                    'nominal'           => $n->amount,
                    'status_transaksi'  => 'PAID',
                    'payment_channel'   => $n->raw['payment_type'] ?? ($n->raw['payment_channel'] ?? 'Virtual Account'),
                    'reference_number'  => $n->raw['reference_number'] ?? $n->transactionId,
                    'raw_response'      => $n->raw,
                ]);

                // Create payment record (BR-09: actual payment date)
                Pembayaran::create([
                    'id_transaksi'       => $transaksi->id_transaksi,
                    'id_tagihan'         => $tagihan->id_tagihan,
                    'tanggal_pembayaran' => \Carbon\Carbon::parse($n->paidAt),
                    'nominal_pembayaran' => $n->amount,
                    'status'             => 'PAID',
                ]);

                // Update invoice status (BR-10, FR-16)
                $tagihan->update(['status' => 'PAID']);

                // Update payment attempt if exists
                if ($attempt) {
                    $attempt->update([
                        'status'                  => 'paid',
                        'provider_transaction_id' => $n->transactionId,
                    ]);
                }

                return $transaksi;
            });

            $this->updateLog($log, 'processed', 'Pembayaran berhasil diproses.', $transaksi->id_transaksi);
            Log::info("Webhook [{$n->provider}] order {$n->orderRef} → invoice {$tagihan->id_tagihan} PAID");
            return WebhookResult::ok('Pembayaran berhasil diproses.');

        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'race_condition_already_paid') {
                $this->updateLog($log, 'duplicate', 'Race condition: invoice already paid by concurrent request.');
                return WebhookResult::duplicate();
            }
            throw $e;
        }
    }

    /** Reject an unauthenticated notification — still logged (BR-17) */
    public function rejectUnauthenticated(string $provider, array $raw): WebhookResult
    {
        $data = (object) $raw;
        $log  = NotifikasiGateway::create([
            'provider'         => $provider,
            'order_id'         => $raw['order_id'] ?? ($raw['transaction_id'] ?? null),
            'transaction_id'   => $raw['transaction_id'] ?? null,
            'signature_valid'  => false,
            'status_provider'  => $raw['transaction_status'] ?? ($raw['status'] ?? null),
            'payload'          => $raw,
            'hasil_proses'     => 'rejected-signature',
            'pesan_proses'     => 'Invalid signature — notification rejected.',
        ]);

        Log::warning("Webhook [{$provider}] invalid signature", ['order_id' => $log->order_id]);
        return WebhookResult::authFailed();
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function appendLog(NormalizedNotification $n, bool $signatureValid): NotifikasiGateway
    {
        return NotifikasiGateway::create([
            'provider'        => $n->provider,
            'order_id'        => $n->orderRef,
            'transaction_id'  => $n->transactionId,
            'signature_valid' => $signatureValid,
            'status_provider' => $n->raw['transaction_status'] ?? ($n->raw['status'] ?? null),
            'payload'         => $n->raw,
            'hasil_proses'    => 'processing',
            'pesan_proses'    => null,
        ]);
    }

    private function updateLog(NotifikasiGateway $log, string $hasil, string $pesan, ?int $idTransaksi = null): void
    {
        // Even though the log is "append-only" conceptually, we update the
        // result columns on the same row we just created (within the same request).
        $log->update([
            'hasil_proses' => $hasil,
            'pesan_proses' => $pesan,
            'id_transaksi' => $idTransaksi,
        ]);
    }

    private function isAlreadyProcessed(NormalizedNotification $n): bool
    {
        // Check by provider transaction_id (unique per BR-12)
        if (TransaksiPaymentGateway::where('transaction_id', $n->transactionId)->exists()) {
            return true;
        }
        // Check by order_id already paid
        if (
            $n->orderRef &&
            PermintaanPembayaran::where('order_id', $n->orderRef)->where('status', 'paid')->exists()
        ) {
            return true;
        }
        return false;
    }
}
