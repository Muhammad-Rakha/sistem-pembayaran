<?php

namespace App\Http\Controllers;

use App\Models\TagihanSpp;
use App\Models\Pembayaran;
use App\Services\Payment\MidtransService;
use App\Services\Payment\Providers\MidtransAdapter;
use App\Services\Payment\Providers\SimulatorAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class SiswaController extends Controller
{
    private function getSiswa()
    {
        return auth()->user()->siswa()->with('kelas.jurusan', 'tahunAjaran', 'virtualAccount')->firstOrFail();
    }

    public function dashboard()
    {
        $siswa  = $this->getSiswa();
        $bulan  = now()->month;
        $tahun  = now()->year;

        $tagihanBulanIni = TagihanSpp::where('id_siswa', $siswa->id_siswa)
            ->where('bulan', $bulan)->where('tahun', $tahun)->first();

        $totalTunggakan  = TagihanSpp::where('id_siswa', $siswa->id_siswa)->where('status', 'OVERDUE')->sum('nominal');
        $jumlahTunggakan = TagihanSpp::where('id_siswa', $siswa->id_siswa)->where('status', 'OVERDUE')->count();

        $riwayatTerakhir = TagihanSpp::with('pembayaran')
            ->where('id_siswa', $siswa->id_siswa)
            ->whereIn('status', ['PAID', 'OVERDUE'])
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->limit(5)->get();

        return view('dashboard.siswa', compact(
            'siswa', 'tagihanBulanIni', 'totalTunggakan', 'jumlahTunggakan', 'riwayatTerakhir'
        ));
    }

    public function tagihan()
    {
        $siswa   = $this->getSiswa();
        $tagihan = TagihanSpp::where('id_siswa', $siswa->id_siswa)
            ->orderByDesc('tahun')->orderByDesc('bulan')->paginate(12);
        return view('siswa.tagihan', compact('siswa', 'tagihan'));
    }

    public function riwayat()
    {
        $siswa   = $this->getSiswa();
        $riwayat = Pembayaran::with('tagihan', 'transaksi')
            ->whereHas('tagihan', fn ($q) => $q->where('id_siswa', $siswa->id_siswa))
            ->latest('tanggal_pembayaran')->paginate(15);
        return view('siswa.riwayat', compact('siswa', 'riwayat'));
    }

    public function virtualAccount()
    {
        $siswa = $this->getSiswa();
        return view('siswa.virtual-account', compact('siswa'));
    }

    /**
     * Show the logged-in student's profile.
     *
     * The profile is read from the siswa row linked through users.id, so the
     * student does not have to enter their name, gender, phone, parent, etc.
     * again after logging in with their NIS.
     */
    public function profile()
    {
        $siswa = $this->getSiswa();
        $user  = auth()->user();

        try {
            $isDefaultPassword = Hash::check($siswa->nis, $user->password);
        } catch (\RuntimeException $e) {
            // Ignore unsupported/legacy password hashes instead of breaking the page.
            $isDefaultPassword = false;
        }

        return view('siswa.profile', compact('siswa', 'user', 'isDefaultPassword'));
    }

    /**
     * Update the contact fields that a student is allowed to maintain.
     */
    public function updateProfile(Request $request)
    {
        $siswa = $this->getSiswa();

        $validated = $request->validateWithBag('updateProfile', [
            'no_hp'  => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'alamat' => ['required', 'string', 'max:500'],
        ], [
            'no_hp.regex'     => 'Nomor HP hanya boleh berisi angka, spasi, tanda +, -, atau kurung.',
            'alamat.required' => 'Alamat wajib diisi.',
        ]);

        $siswa->update($validated);

        return redirect()
            ->route('siswa.profile')
            ->with('success', 'Data kontak berhasil diperbarui.');
    }

    /**
     * Change the student's password. The default password is their NIS.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
            'password.min'              => 'Password baru minimal 8 karakter.',
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return redirect()
            ->route('siswa.profile')
            ->with('success', 'Password berhasil diubah.');
    }

    /** Show the payment options page */
    public function bayar(TagihanSpp $tagihan, MidtransAdapter $midtrans, SimulatorAdapter $simulator)
    {
        $siswa = $this->getSiswa();
        abort_if($tagihan->id_siswa !== $siswa->id_siswa, 403);
        abort_if($tagihan->status === 'PAID', 409, 'Tagihan sudah lunas.');
        $banks            = config('midtrans.banks', ['bca', 'bni', 'bri', 'permata']);
        $midtransEnabled  = $midtrans->isEnabled();
        $simulatorEnabled = $simulator->isEnabled();
        return view('siswa.bayar', compact('siswa', 'tagihan', 'banks', 'midtransEnabled', 'simulatorEnabled'));
    }

    /** POST — create Midtrans transaction and show VA instructions */
    public function generateMidtrans(TagihanSpp $tagihan, Request $request, MidtransService $midtrans, MidtransAdapter $gateway)
    {
        $siswa = $this->getSiswa();
        abort_if($tagihan->id_siswa !== $siswa->id_siswa, 403);
        abort_if($tagihan->status === 'PAID', 409, 'Tagihan sudah lunas.');

        if (!$gateway->isEnabled()) {
            return back()->with('error', 'Midtrans belum dikonfigurasi. Isi MIDTRANS_SERVER_KEY di file .env.');
        }

        $request->validate(['bank' => 'required|in:bca,bni,bri,permata']);
        try {
            $attempt = $midtrans->createTransaction($tagihan, $siswa, $request->bank);
            return view('siswa.payment-instructions', compact('siswa', 'tagihan', 'attempt'));
        } catch (\Exception $e) {
            Log::error('Midtrans createTransaction failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat transaksi Midtrans: ' . $e->getMessage());
        }
    }

    /** GET — show the simulator page */
    public function generateSimulator(TagihanSpp $tagihan)
    {
        abort_unless(config('midtrans.simulator_enabled') && !config('midtrans.is_production'), 403, 'Simulator disabled.');
        $siswa = $this->getSiswa();
        abort_if($tagihan->id_siswa !== $siswa->id_siswa, 403);
        abort_if($tagihan->status === 'PAID', 409, 'Tagihan sudah lunas.');
        $siswa->load('virtualAccount');
        $transactionId = 'TRX-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        return view('siswa.generate-payment', compact('siswa', 'tagihan', 'transactionId'));
    }

    /**
     * POST — signs the payload server-side and POSTs to the simulator webhook.
     * This keeps the HMAC secret server-side (never exposed to browser).
     */
    public function sendSimulatorWebhook(TagihanSpp $tagihan, Request $request)
    {
        abort_unless(config('midtrans.simulator_enabled') && !config('midtrans.is_production'), 403);
        $siswa = $this->getSiswa();
        abort_if($tagihan->id_siswa !== $siswa->id_siswa, 403);
        abort_if($tagihan->status === 'PAID', 409, 'Tagihan sudah lunas.');

        $request->validate([
            'virtual_account' => 'required|string',
            'amount'          => 'required|integer|min:1',
            'transaction_id'  => 'required|string',
            'status'          => 'required|in:PAID,FAILED',
            'paid_at'         => 'required|string',
        ]);

        $payload  = $request->only('virtual_account', 'amount', 'transaction_id', 'status', 'paid_at');
        $payload['amount'] = (int) $payload['amount'];
        $rawBody  = json_encode($payload);
        $secret   = config('midtrans.simulator_secret', '');
        $signature = hash_hmac('sha256', $rawBody, $secret);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type'          => 'application/json',
                'X-Simulator-Signature' => $signature,
            ])->post(url('/webhooks/simulator'), $payload);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
