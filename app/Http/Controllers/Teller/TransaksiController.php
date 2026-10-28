<?php

namespace App\Http\Controllers\Teller;

use App\Http\Controllers\Controller;
use App\Models\TransaksiPaymentGateway;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $transaksi = TransaksiPaymentGateway::with('siswa', 'pembayaran')
            ->when($q, fn ($query) => $query
                ->where('transaction_id', 'like', "%$q%")
                ->orWhere('virtual_account', 'like', "%$q%")
                ->orWhereHas('siswa', fn ($s) => $s->where('nama', 'like', "%$q%")->orWhere('nis', 'like', "%$q%"))
            )
            ->latest('created_at')->paginate(20)->withQueryString();
        return view('teller.transaksi.index', compact('transaksi', 'q'));
    }

    public function show(TransaksiPaymentGateway $transaksi)
    {
        $transaksi->load('siswa.kelas', 'pembayaran.tagihan');
        return view('teller.transaksi.show', compact('transaksi'));
    }
}
