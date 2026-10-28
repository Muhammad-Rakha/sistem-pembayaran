<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $kelas = Kelas::with('jurusan', 'tahunAjaran')
            ->when($q, fn ($query) => $query->where('nama_kelas', 'like', "%$q%"))
            ->orderBy('nama_kelas')
            ->paginate(15)
            ->withQueryString();
        return view('admin.kelas.index', compact('kelas', 'q'));
    }

    public function create()
    {
        $jurusan    = Jurusan::orderBy('nama_jurusan')->get();
        $tahunAjaran = TahunAjaran::orderByDesc('tanggal_mulai')->get();
        return view('admin.kelas.create', compact('jurusan', 'tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'       => 'required|string|max:50',
            'tingkat'          => 'required|in:X,XI,XII',
            'id_jurusan'       => 'required|exists:jurusan,id_jurusan',
            'id_tahun_ajaran'  => 'required|exists:tahun_ajaran,id_tahun_ajaran',
        ]);
        Kelas::create($request->only('nama_kelas', 'tingkat', 'id_jurusan', 'id_tahun_ajaran'));
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        $jurusan     = Jurusan::orderBy('nama_jurusan')->get();
        $tahunAjaran = TahunAjaran::orderByDesc('tanggal_mulai')->get();
        return view('admin.kelas.edit', compact('kelas', 'jurusan', 'tahunAjaran'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas'       => 'required|string|max:50',
            'tingkat'          => 'required|in:X,XI,XII',
            'id_jurusan'       => 'required|exists:jurusan,id_jurusan',
            'id_tahun_ajaran'  => 'required|exists:tahun_ajaran,id_tahun_ajaran',
        ]);
        $kelas->update($request->only('nama_kelas', 'tingkat', 'id_jurusan', 'id_tahun_ajaran'));
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas dihapus.');
    }
}
