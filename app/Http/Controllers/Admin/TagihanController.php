<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanSpp;
use App\Models\Siswa;
use App\Models\TarifSpp;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->q;
        $status = $request->status;
        $bulan  = $request->bulan;
        $tahun  = $request->tahun ?? now()->year;

        $tagihan = TagihanSpp::with('siswa.kelas', 'pembayaran')
            ->when($q, fn ($query) => $query->whereHas('siswa', fn ($s) => $s
                ->where('nama', 'like', "%$q%")->orWhere('nis', 'like', "%$q%")))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($bulan, fn ($query) => $query->where('bulan', $bulan))
            ->when($tahun, fn ($query) => $query->where('tahun', $tahun))
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(20)
            ->withQueryString();

        $tahunAjaran = TahunAjaran::where('status', 'aktif')->get();
        $tarifAktif  = TarifSpp::where('status', 'aktif')->with('tahunAjaran')->get();

        return view('admin.tagihan.index', compact('tagihan', 'q', 'status', 'bulan', 'tahun', 'tarifAktif', 'tahunAjaran'));
    }

    /**
     * Generate tagihan bulanan untuk semua siswa aktif berdasarkan tarif.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'id_tarif' => 'required|exists:tarif_spp,id_tarif',
            'bulan'    => 'required|integer|min:1|max:12',
            'tahun'    => 'required|integer|min:2000|max:2100',
        ]);

        $tarif = TarifSpp::findOrFail($request->id_tarif);
        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        // Tanggal jatuh tempo: hari terakhir bulan tersebut (BR-05)
        $jatuhTempo = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->toDateString();

        $siswaList = Siswa::where('status', 'aktif')->get();
        $generated = 0;
        foreach ($siswaList as $siswa) {
            // Skip jika tagihan sudah ada (unique constraint siswa+bulan+tahun)
            $exists = TagihanSpp::where('id_siswa', $siswa->id_siswa)
                ->where('bulan', $bulan)->where('tahun', $tahun)->exists();
            if (!$exists) {
                TagihanSpp::create([
                    'id_siswa'         => $siswa->id_siswa,
                    'id_tarif'         => $tarif->id_tarif,
                    'id_tahun_ajaran'  => $tarif->id_tahun_ajaran,
                    'bulan'            => $bulan,
                    'tahun'            => $tahun,
                    'nominal'          => $tarif->nominal,
                    'tanggal_jatuh_tempo' => $jatuhTempo,
                    'status'           => 'UNPAID',
                ]);
                $generated++;
            }
        }
        return redirect()->route('admin.tagihan.index')->with('success', "Berhasil generate $generated tagihan untuk $bulan/$tahun.");
    }

    public function show(TagihanSpp $tagihan)
    {
        $tagihan->load('siswa.kelas.jurusan', 'siswa.virtualAccount', 'tarif', 'pembayaran.transaksi');
        return view('admin.tagihan.show', compact('tagihan'));
    }

    /**
     * Manually trigger the overdue job with an optional reference date (for demo, §11).
     * This exercises the same Artisan command as the scheduler.
     */
    public function triggerOverdue(Request $request)
    {
        $referenceDate = $request->input('reference_date', now()->toDateString());
        $updated = TagihanSpp::where('status', 'UNPAID')
            ->where('tanggal_jatuh_tempo', '<', $referenceDate)
            ->update(['status' => 'OVERDUE']);
        return redirect()->route('admin.tagihan.index')
            ->with('success', "$updated tagihan ditandai OVERDUE (referensi: $referenceDate).");
    }
}
