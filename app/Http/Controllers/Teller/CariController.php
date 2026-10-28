<?php

namespace App\Http\Controllers\Teller;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\VirtualAccount;
use App\Models\TransaksiPaymentGateway;
use Illuminate\Http\Request;

class CariController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->q;
        $result = null;

        if ($q) {
            // Search by NIS, NISN, name, VA number, or transaction ID
            $siswa = Siswa::with('kelas', 'virtualAccount', 'tagihan.pembayaran')
                ->where('nis', 'like', "%$q%")
                ->orWhere('nisn', 'like', "%$q%")
                ->orWhere('nama', 'like', "%$q%")
                ->orWhereHas('virtualAccount', fn ($va) => $va->where('nomor_va', 'like', "%$q%"))
                ->get();

            $transaksi = TransaksiPaymentGateway::with('siswa', 'pembayaran')
                ->where('transaction_id', 'like', "%$q%")
                ->orWhere('virtual_account', 'like', "%$q%")
                ->get();

            $result = compact('siswa', 'transaksi');
        }

        return view('teller.cari', compact('q', 'result'));
    }
}
