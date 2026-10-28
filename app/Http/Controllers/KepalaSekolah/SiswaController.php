<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $siswa = Siswa::with('kelas', 'tahunAjaran', 'virtualAccount')
            ->when($q, fn ($query) => $query
                ->where('nama', 'like', "%$q%")
                ->orWhere('nis', 'like', "%$q%")
                ->orWhere('nisn', 'like', "%$q%")
            )
            ->orderBy('nama')->paginate(20)->withQueryString();
        return view('kepsek.siswa.index', compact('siswa', 'q'));
    }

    public function show(Siswa $siswa)
    {
        $siswa->load('kelas.jurusan', 'tahunAjaran', 'virtualAccount', 'tagihan.pembayaran');
        return view('kepsek.siswa.show', compact('siswa'));
    }
}
