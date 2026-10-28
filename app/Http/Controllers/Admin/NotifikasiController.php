<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiGateway;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $q        = $request->q;
        $provider = $request->provider;
        $hasil    = $request->hasil;

        $notifikasi = NotifikasiGateway::with('transaksi')
            ->when($q, fn ($query) => $query
                ->where('transaction_id', 'like', "%$q%")
                ->orWhere('order_id', 'like', "%$q%"))
            ->when($provider, fn ($query) => $query->where('provider', $provider))
            ->when($hasil,    fn ($query) => $query->where('hasil_proses', $hasil))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.notifikasi.index', compact('notifikasi', 'q', 'provider', 'hasil'));
    }

    public function show(NotifikasiGateway $notifikasi)
    {
        $notifikasi->load('transaksi.pembayaran.tagihan.siswa');
        return view('admin.notifikasi.show', compact('notifikasi'));
    }
}
