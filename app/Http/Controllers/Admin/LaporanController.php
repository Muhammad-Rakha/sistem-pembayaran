<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\TagihanSpp;
use App\Models\TahunAjaran;
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
            'id_tahun_ajaran'=> $request->id_tahun_ajaran,
            'id_kelas'       => $request->id_kelas,
            'status'         => $request->status,
            'q'              => $request->q,
        ];
    }

    public function pembayaran(Request $request)
    {
        $filters = $this->getFilters($request);

        $pembayaran = Pembayaran::with('tagihan.siswa.kelas', 'transaksi')
            ->when($filters['tanggal_dari'],   fn ($q) => $q->whereDate('tanggal_pembayaran', '>=', $filters['tanggal_dari']))
            ->when($filters['tanggal_sampai'], fn ($q) => $q->whereDate('tanggal_pembayaran', '<=', $filters['tanggal_sampai']))
            ->when($filters['bulan'],  fn ($q) => $q->whereHas('tagihan', fn ($t) => $t->where('bulan', $filters['bulan'])))
            ->when($filters['tahun'],  fn ($q) => $q->whereHas('tagihan', fn ($t) => $t->where('tahun', $filters['tahun'])))
            ->when($filters['id_kelas'], fn ($q) => $q->whereHas('tagihan.siswa', fn ($s) => $s->where('id_kelas', $filters['id_kelas'])))
            ->when($filters['q'], fn ($q) => $q->whereHas('tagihan.siswa', fn ($s) =>
                $s->where('nama', 'like', "%{$filters['q']}%")->orWhere('nis', 'like', "%{$filters['q']}%")
            ))
            ->latest('tanggal_pembayaran')
            ->paginate(20)
            ->withQueryString();

        $tahunAjaran = TahunAjaran::orderByDesc('tanggal_mulai')->get();
        $kelas       = Kelas::orderBy('nama_kelas')->get();

        return view('admin.laporan.pembayaran', compact('pembayaran', 'filters', 'tahunAjaran', 'kelas'));
    }

    public function tunggakan(Request $request)
    {
        $filters = $this->getFilters($request);

        $tunggakan = TagihanSpp::with('siswa.kelas')
            ->where('status', 'OVERDUE')
            ->when($filters['bulan'],    fn ($q) => $q->where('bulan', $filters['bulan']))
            ->when($filters['tahun'],    fn ($q) => $q->where('tahun', $filters['tahun']))
            ->when($filters['id_kelas'], fn ($q) => $q->whereHas('siswa', fn ($s) => $s->where('id_kelas', $filters['id_kelas'])))
            ->when($filters['q'], fn ($q) => $q->whereHas('siswa', fn ($s) =>
                $s->where('nama', 'like', "%{$filters['q']}%")->orWhere('nis', 'like', "%{$filters['q']}%")
            ))
            ->orderBy('tahun')->orderBy('bulan')
            ->paginate(20)
            ->withQueryString();

        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('admin.laporan.tunggakan', compact('tunggakan', 'filters', 'kelas'));
    }

    public function exportPembayaran(Request $request)
    {
        $filters = $this->getFilters($request);
        return Excel::download(new LaporanPembayaranExport($filters), 'laporan-pembayaran.xlsx');
    }

    public function exportTunggakan(Request $request)
    {
        $filters = $this->getFilters($request);
        return Excel::download(new LaporanTunggakanExport($filters), 'laporan-tunggakan.xlsx');
    }

    public function rekap(Request $request)
    {
        $tahun  = $request->tahun ?? now()->year;
        $idKelas = $request->id_kelas;

        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $rekap = [];
        for ($b = 1; $b <= 12; $b++) {
            $query = \App\Models\TagihanSpp::where('tahun', $tahun)->where('bulan', $b)
                ->when($idKelas, fn ($q) => $q->whereHas('siswa', fn ($s) => $s->where('id_kelas', $idKelas)));
            $rekap[$b] = [
                'bulan'    => $namaBulan[$b],
                'total'    => $query->count(),
                'lunas'    => (clone $query)->where('status', 'PAID')->count(),
                'belum'    => (clone $query)->where('status', 'UNPAID')->count(),
                'tunggakan'=> (clone $query)->where('status', 'OVERDUE')->count(),
                'nominal'  => (clone $query)->where('status', 'PAID')->sum('nominal'),
            ];
        }

        $kelas = \App\Models\Kelas::orderBy('nama_kelas')->get();
        return view('admin.laporan.rekap', compact('rekap', 'tahun', 'idKelas', 'kelas'));
    }
}
