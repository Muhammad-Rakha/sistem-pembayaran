<?php

namespace App\Http\Controllers\KepalaSekolah;

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
        return view('kepsek.transaksi.index', compact('transaksi', 'q'));
    }
}
