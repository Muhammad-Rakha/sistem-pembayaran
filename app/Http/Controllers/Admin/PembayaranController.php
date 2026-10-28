<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $pembayaran = Pembayaran::with('tagihan.siswa.kelas', 'transaksi')
            ->when($q, fn ($query) => $query->whereHas('tagihan.siswa', fn ($s) =>
                $s->where('nama', 'like', "%$q%")->orWhere('nis', 'like', "%$q%")
            ))
            ->latest('tanggal_pembayaran')
            ->paginate(20)
            ->withQueryString();
        return view('admin.pembayaran.index', compact('pembayaran', 'q'));
    }
}
