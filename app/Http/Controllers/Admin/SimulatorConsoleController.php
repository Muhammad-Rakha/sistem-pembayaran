<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\TagihanSpp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Admin test console for the Local Simulator (§10.5, FR-33).
 * Sends crafted payloads through the webhook endpoint so the full
 * validation & recording core is exercised (not a shortcut).
 * Disabled when SIMULATOR_ENABLED=false or MIDTRANS_IS_PRODUCTION=true.
 */
class SimulatorConsoleController extends Controller
{
    public function __construct()
    {
        abort_unless(
            config('midtrans.simulator_enabled') && !config('midtrans.is_production'),
            403,
            'Simulator console is disabled in production or when SIMULATOR_ENABLED=false.'
        );
    }

    public function index()
    {
        $siswaList = Siswa::with('virtualAccount', 'tagihan')->where('status', 'aktif')->get();
        $tagihanList = TagihanSpp::with('siswa.virtualAccount')
            ->whereIn('status', ['UNPAID', 'OVERDUE'])
            ->orderBy('tahun')->orderBy('bulan')
            ->get();
        return view('admin.simulator-console', compact('siswaList', 'tagihanList'));
    }

    public function kirim(Request $request)
    {
        $request->validate([
            'virtual_account' => 'required|string',
            'amount'          => 'required|integer|min:1',
            'transaction_id'  => 'required|string',
            'status'          => 'required|in:PAID,FAILED',
            'paid_at'         => 'required|date',
        ]);

        $payload = [
            'virtual_account' => $request->virtual_account,
            'amount'          => (int) $request->amount,
            'transaction_id'  => $request->transaction_id,
            'status'          => $request->status,
            'paid_at'         => $request->paid_at,
        ];

        $rawBody  = json_encode($payload);
        $secret   = config('midtrans.simulator_secret', '');
        $signature = hash_hmac('sha256', $rawBody, $secret);

        $webhookUrl = url('/webhooks/simulator');

        try {
            $response = Http::withHeaders([
                'Content-Type'           => 'application/json',
                'X-Simulator-Signature'  => $signature,
            ])->post($webhookUrl, $payload);

            $result = $response->json();
        } catch (\Exception $e) {
            $result = ['success' => false, 'message' => $e->getMessage()];
        }

        return back()->with('simulator_result', [
            'payload'   => $payload,
            'signature' => $signature,
            'response'  => $result,
        ]);
    }
}
