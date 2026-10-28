<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\TagihanSpp;
use App\Models\TransaksiPaymentGateway;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa      = Siswa::where('status', 'aktif')->count();
        $totalTagihan    = TagihanSpp::count();
        $totalPembayaran = Pembayaran::count();
        $totalTunggakan  = TagihanSpp::where('status', 'OVERDUE')->count();
        $nominalTunggakan = TagihanSpp::where('status', 'OVERDUE')->sum('nominal');
        $totalTransaksi  = TransaksiPaymentGateway::count();

        // Chart: pembayaran per bulan (12 bulan terakhir)
        $chartPembayaran = Pembayaran::select(
                DB::raw('MONTH(tanggal_pembayaran) as bulan'),
                DB::raw('YEAR(tanggal_pembayaran) as tahun'),
                DB::raw('COUNT(*) as jumlah'),
                DB::raw('SUM(nominal_pembayaran) as total')
            )
            ->whereYear('tanggal_pembayaran', now()->year)
            ->groupBy('tahun', 'bulan')
            ->orderBy('bulan')
            ->get();

        $chartLabels  = [];
        $chartData    = [];
        $namaBulan    = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        foreach ($chartPembayaran as $c) {
            $chartLabels[] = $namaBulan[$c->bulan] . ' ' . $c->tahun;
            $chartData[]   = (int) $c->total;
        }

        // Chart tunggakan per bulan
        $chartTunggakan = TagihanSpp::where('status', 'OVERDUE')
            ->select(DB::raw('bulan, tahun, COUNT(*) as jumlah'))
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')->orderBy('bulan')
            ->limit(12)->get();

        $tunggakanLabels = [];
        $tunggakanData   = [];
        foreach ($chartTunggakan as $t) {
            $tunggakanLabels[] = $namaBulan[$t->bulan] . ' ' . $t->tahun;
            $tunggakanData[]   = (int) $t->jumlah;
        }

        $recentTransaksi = TransaksiPaymentGateway::with('siswa')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalSiswa', 'totalTagihan', 'totalPembayaran',
            'totalTunggakan', 'nominalTunggakan', 'totalTransaksi',
            'chartLabels', 'chartData',
            'tunggakanLabels', 'tunggakanData',
            'recentTransaksi'
        ));
    }
}
