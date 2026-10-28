<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermintaanPembayaran;
use App\Services\Payment\MidtransService;
use App\Services\Payment\WebhookService;
use App\Services\Payment\Providers\MidtransAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Allows admin to manually pull Midtrans transaction status
 * and re-process the notification if the webhook was not received
 * (e.g. because localhost can't receive Midtrans callbacks).
 *
 * This is the correct workaround when running locally without a tunnel.
 * The status is fetched server-side via the Transaction Status API,
 * then fed through the same MidtransAdapter + WebhookService core —
 * NOT a direct manual status change.
 */
class MidtransCheckController extends Controller
{
    public function __construct(
        private readonly MidtransService  $midtransService,
        private readonly MidtransAdapter  $adapter,
        private readonly WebhookService   $webhookCore,
    ) {}

    /** Show all waiting Midtrans attempts */
    public function check(string $orderId)
    {
        $attempt = PermintaanPembayaran::where('order_id', $orderId)
            ->where('provider', 'midtrans')
            ->with('tagihan.siswa.virtualAccount')
            ->firstOrFail();

        $statusResponse = null;
        $error          = null;

        try {
            $statusResponse = $this->midtransService->getStatus($orderId);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('admin.midtrans-check', compact('attempt', 'statusResponse', 'error'));
    }

    /**
     * Pull the Midtrans status and feed it into the webhook core.
     * This processes the payment exactly as a real webhook would —
     * including signature skipping (we already authenticated via Server Key API).
     */
    public function process(string $orderId, Request $request)
    {
        $attempt = PermintaanPembayaran::where('order_id', $orderId)
            ->where('provider', 'midtrans')
            ->firstOrFail();

        try {
            // Fetch real status from Midtrans Transaction Status API
            $statusData = $this->midtransService->getStatus($orderId);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengambil status Midtrans: ' . $e->getMessage());
        }

        Log::info('MidtransCheck: manual process', ['order_id' => $orderId, 'status' => $statusData['transaction_status'] ?? '']);

        // Reuse adapter's normalize() — build a fake request with the API response
        $fakeRequest = Request::create('/webhooks/midtrans', 'POST', $statusData);
        $notification = $this->adapter->normalize($fakeRequest);

        // Feed through the shared webhook core
        $result = $this->webhookCore->process($notification);

        if ($result->httpCode === 200) {
            return redirect()->route('admin.transaksi.index')
                ->with('success', 'Status berhasil diproses: ' . $result->message);
        }

        return back()->with('error', 'Gagal diproses: ' . $result->message);
    }
}
