<?php

namespace App\Http\Controllers\Teller;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanSpp;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPembayaranExport;
use App\Exports\LaporanTunggakanExport;

class LaporanController extends Controller
{
    protected function getFilters(Request $request): array
    {
        return [
            'tanggal_dari'   => $request->tanggal_dari,
            'tanggal_sampai' => $request->tanggal_sampai,
            'bulan'          => $request->bulan,
            'tahun'          => $request->tahun,
            'id_kelas'       => $request->id_kelas,
            'q'              => $request->q,
        ];
    }

    public function pembayaran(Request $request)
    {
        $filters = $this->getFilters($request);
        $pembayaran = Pembayaran::with('tagihan.siswa.kelas', 'transaksi')
            ->when($filters['tanggal_dari'],   fn ($q) => $q->whereDate('tanggal_pembayaran', '>=', $filters['tanggal_dari']))
            ->when($filters['tanggal_sampai'], fn ($q) => $q->whereDate('tanggal_pembayaran', '<=', $filters['tanggal_sampai']))
            ->when($filters['bulan'], fn ($q) => $q->whereHas('tagihan', fn ($t) => $t->where('bulan', $filters['bulan'])))
            ->when($filters['tahun'], fn ($q) => $q->whereHas('tagihan', fn ($t) => $t->where('tahun', $filters['tahun'])))
            ->latest('tanggal_pembayaran')->paginate(20)->withQueryString();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('teller.laporan.pembayaran', compact('pembayaran', 'filters', 'kelas'));
    }

    public function tunggakan(Request $request)
    {
        $filters = $this->getFilters($request);
        $tunggakan = TagihanSpp::with('siswa.kelas')->where('status', 'OVERDUE')
            ->when($filters['bulan'], fn ($q) => $q->where('bulan', $filters['bulan']))
            ->when($filters['tahun'], fn ($q) => $q->where('tahun', $filters['tahun']))
            ->orderBy('tahun')->orderBy('bulan')->paginate(20)->withQueryString();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('teller.laporan.tunggakan', compact('tunggakan', 'filters', 'kelas'));
    }

    public function exportPembayaran(Request $request)
    {
        return Excel::download(new LaporanPembayaranExport($this->getFilters($request)), 'laporan-pembayaran.xlsx');
    }

    public function exportTunggakan(Request $request)
    {
        return Excel::download(new LaporanTunggakanExport($this->getFilters($request)), 'laporan-tunggakan.xlsx');
    }
}
