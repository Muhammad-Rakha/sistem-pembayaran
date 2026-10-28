<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\TagihanSpp;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswaAktif  = Siswa::where('status', 'aktif')->count();
        $siswaSudahBayar  = TagihanSpp::where('status', 'PAID')->where('bulan', now()->month)->where('tahun', now()->year)->distinct('id_siswa')->count('id_siswa');
        $siswaBelumBayar  = TagihanSpp::where('status', 'UNPAID')->where('bulan', now()->month)->where('tahun', now()->year)->distinct('id_siswa')->count('id_siswa');
        $siswaOverdue     = TagihanSpp::where('status', 'OVERDUE')->distinct('id_siswa')->count('id_siswa');
        $totalDiterima    = Pembayaran::sum('nominal_pembayaran');
        $totalTunggakan   = TagihanSpp::where('status', 'OVERDUE')->sum('nominal');

        $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        $chartBayar = Pembayaran::select(
                DB::raw('MONTH(tanggal_pembayaran) as bulan'),
                DB::raw('SUM(nominal_pembayaran) as total')
            )
            ->whereYear('tanggal_pembayaran', now()->year)
            ->groupBy('bulan')->orderBy('bulan')->get();

        $chartLabels = [];
        $chartData   = [];
        foreach ($chartBayar as $c) {
            $chartLabels[] = $namaBulan[$c->bulan];
            $chartData[]   = (int) $c->total;
        }

        $chartTunggakan = TagihanSpp::where('status', 'OVERDUE')
            ->select(DB::raw('bulan, COUNT(*) as jumlah'))
            ->whereYear('created_at', now()->year)
            ->groupBy('bulan')->orderBy('bulan')->get();

        $tunggakanLabels = [];
        $tunggakanData   = [];
        foreach ($chartTunggakan as $t) {
            $tunggakanLabels[] = $namaBulan[$t->bulan];
            $tunggakanData[]   = (int) $t->jumlah;
        }

        return view('dashboard.kepala-sekolah', compact(
            'totalSiswaAktif', 'siswaSudahBayar', 'siswaBelumBayar', 'siswaOverdue',
            'totalDiterima', 'totalTunggakan',
            'chartLabels', 'chartData', 'tunggakanLabels', 'tunggakanData'
        ));
    }
}
