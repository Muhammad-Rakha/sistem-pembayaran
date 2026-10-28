<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\KepalaSekolah;
use App\Http\Controllers\Teller;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\PaymentGatewayController;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('auth')->group(function () {

    // Redirect /dashboard ke role masing-masing
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match (true) {
            $user->hasRole('administrator')  => redirect()->route('admin.dashboard'),
            $user->hasRole('kepala_sekolah') => redirect()->route('kepala-sekolah.dashboard'),
            $user->hasRole('teller')         => redirect()->route('teller.dashboard'),
            default                          => redirect()->route('siswa.dashboard'),
        };
    })->name('dashboard');

    // =========================================================
    // ADMINISTRATOR
    // =========================================================
    Route::middleware('role:administrator')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', Admin\UserController::class)->except(['show']);

        // Master data
        Route::resource('siswa', Admin\SiswaController::class);
        Route::resource('jurusan', Admin\JurusanController::class)->except(['show']);
        Route::resource('kelas', Admin\KelasController::class)->except(['show']);
        Route::resource('tahun-ajaran', Admin\TahunAjaranController::class)->parameters(['tahun-ajaran' => 'tahunAjaran'])->except(['show']);
        Route::resource('tarif-spp', Admin\TarifSppController::class)->parameters(['tarif-spp' => 'tarifSpp'])->except(['show']);
        Route::resource('virtual-account', Admin\VirtualAccountController::class)->parameters(['virtual-account' => 'virtualAccount']);

        // Tagihan
        Route::get('/tagihan', [Admin\TagihanController::class, 'index'])->name('tagihan.index');
        Route::post('/tagihan/generate', [Admin\TagihanController::class, 'generate'])->name('tagihan.generate');
        Route::get('/tagihan/{tagihan}', [Admin\TagihanController::class, 'show'])->name('tagihan.show');

        // Transaksi & Pembayaran
        Route::get('/transaksi', [Admin\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/{transaksi}', [Admin\TransaksiController::class, 'show'])->name('transaksi.show');
        Route::get('/pembayaran', [Admin\PembayaranController::class, 'index'])->name('pembayaran.index');

        // Laporan
        Route::get('/laporan/pembayaran', [Admin\LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');
        Route::get('/laporan/tunggakan', [Admin\LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
        Route::get('/laporan/pembayaran/export', [Admin\LaporanController::class, 'exportPembayaran'])->name('laporan.pembayaran.export');
        Route::get('/laporan/tunggakan/export', [Admin\LaporanController::class, 'exportTunggakan'])->name('laporan.tunggakan.export');
        Route::get('/laporan/rekap', [Admin\LaporanController::class, 'rekap'])->name('laporan.rekap');

        // Simulator test console
        Route::get('/simulator-console', [Admin\SimulatorConsoleController::class, 'index'])->name('simulator.console');
        Route::post('/simulator-console/kirim', [Admin\SimulatorConsoleController::class, 'kirim'])->name('simulator.console.kirim');

        // Notification log
        Route::get('/notifikasi', [Admin\NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/{notifikasi}', [Admin\NotifikasiController::class, 'show'])->name('notifikasi.show');

        // Overdue trigger (manual for demo)
        Route::post('/tagihan/trigger-overdue', [Admin\TagihanController::class, 'triggerOverdue'])->name('tagihan.trigger-overdue');

        // Manual webhook check — pull Midtrans transaction status and process it
        Route::get('/midtrans/check-status/{orderId}', [Admin\MidtransCheckController::class, 'check'])->name('midtrans.check');
        Route::post('/midtrans/process/{orderId}', [Admin\MidtransCheckController::class, 'process'])->name('midtrans.process');
    });

    // =========================================================
    // KEPALA SEKOLAH
    // =========================================================
    Route::middleware('role:kepala_sekolah')->prefix('kepala-sekolah')->name('kepala-sekolah.')->group(function () {
        Route::get('/dashboard', [KepalaSekolah\DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('role:kepala_sekolah')->prefix('kepsek')->name('kepsek.')->group(function () {
        Route::get('/siswa', [KepalaSekolah\SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/siswa/{siswa}', [KepalaSekolah\SiswaController::class, 'show'])->name('siswa.show');
        Route::get('/tagihan', [KepalaSekolah\TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/transaksi', [KepalaSekolah\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/laporan/pembayaran', [KepalaSekolah\LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');
        Route::get('/laporan/tunggakan', [KepalaSekolah\LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
        Route::get('/laporan/pembayaran/export', [KepalaSekolah\LaporanController::class, 'exportPembayaran'])->name('laporan.pembayaran.export');
        Route::get('/laporan/tunggakan/export', [KepalaSekolah\LaporanController::class, 'exportTunggakan'])->name('laporan.tunggakan.export');
    });

    // =========================================================
    // TELLER
    // =========================================================
    Route::middleware('role:teller')->prefix('teller')->name('teller.')->group(function () {
        Route::get('/dashboard', [Teller\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/cari', [Teller\CariController::class, 'index'])->name('cari');
        Route::get('/transaksi', [Teller\TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/{transaksi}', [Teller\TransaksiController::class, 'show'])->name('transaksi.show');
        Route::get('/tagihan', [Teller\TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/laporan/pembayaran', [Teller\LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');
        Route::get('/laporan/tunggakan', [Teller\LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
        Route::get('/laporan/pembayaran/export', [Teller\LaporanController::class, 'exportPembayaran'])->name('laporan.pembayaran.export');
        Route::get('/laporan/tunggakan/export', [Teller\LaporanController::class, 'exportTunggakan'])->name('laporan.tunggakan.export');
    });

    // =========================================================
    // SISWA
    // =========================================================
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/tagihan', [SiswaController::class, 'tagihan'])->name('tagihan');
        Route::get('/riwayat', [SiswaController::class, 'riwayat'])->name('riwayat');
        Route::get('/virtual-account', [SiswaController::class, 'virtualAccount'])->name('virtual-account');
        // Profile
        Route::get('/profil', [SiswaController::class, 'profile'])->name('profile');
        Route::put('/profil', [SiswaController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profil/password', [SiswaController::class, 'updatePassword'])->name('profile.password');
        // Payment flows
        Route::get('/tagihan/{tagihan}/bayar',                        [SiswaController::class, 'bayar'])->name('tagihan.bayar');
        Route::post('/tagihan/{tagihan}/bayar/midtrans',              [SiswaController::class, 'generateMidtrans'])->name('tagihan.generate-midtrans');
        Route::get('/tagihan/{tagihan}/bayar/simulator',              [SiswaController::class, 'generateSimulator'])->name('tagihan.generate-simulator');
        Route::post('/tagihan/{tagihan}/bayar/simulator/send',        [SiswaController::class, 'sendSimulatorWebhook'])->name('tagihan.generate-simulator-send');
    });
});

// =========================================================
// WEBHOOK ENDPOINTS — no auth/session middleware (§10.9)
// Authentication handled by provider signature verification
// =========================================================
Route::post('/webhooks/midtrans',   [PaymentGatewayController::class, 'midtrans'])->name('webhook.midtrans');
Route::post('/webhooks/simulator',  [PaymentGatewayController::class, 'simulator'])->name('webhook.simulator');

require __DIR__.'/auth.php';
