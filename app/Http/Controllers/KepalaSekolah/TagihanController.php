<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\TagihanSpp;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->q;
        $status = $request->status;
        $bulan  = $request->bulan;
        $tahun  = $request->tahun ?? now()->year;

        $tagihan = TagihanSpp::with('siswa.kelas', 'pembayaran')
            ->when($q, fn ($query) => $query->whereHas('siswa', fn ($s) =>
                $s->where('nama', 'like', "%$q%")->orWhere('nis', 'like', "%$q%")))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($bulan, fn ($query) => $query->where('bulan', $bulan))
            ->when($tahun, fn ($query) => $query->where('tahun', $tahun))
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(20)->withQueryString();

        return view('kepsek.tagihan.index', compact('tagihan', 'q', 'status', 'bulan', 'tahun'));
    }
}
