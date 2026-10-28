<?php

namespace App\Http\Controllers\Teller;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPaymentGateway;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $transaksiHariIni = TransaksiPaymentGateway::whereDate('created_at', $today)->count();
        $nominalHariIni   = Pembayaran::whereDate('tanggal_pembayaran', $today)->sum('nominal_pembayaran');
        $recentTransaksi  = TransaksiPaymentGateway::with('siswa')->latest('created_at')->limit(10)->get();

        return view('dashboard.teller', compact('transaksiHariIni', 'nominalHariIni', 'recentTransaksi'));
    }
}
