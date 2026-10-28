<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\VirtualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                ->orWhereHas('virtualAccount', fn ($va) => $va->where('nomor_va', 'like', "%$q%"))
            )
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();
        return view('admin.siswa.index', compact('siswa', 'q'));
    }

    public function create()
    {
        $kelas       = Kelas::with('jurusan')->orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::where('status', 'aktif')->orderByDesc('tanggal_mulai')->get();
        return view('admin.siswa.create', compact('kelas', 'tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis'            => 'required|string|max:20|unique:siswa,nis',
            'nisn'           => 'required|string|max:20|unique:siswa,nisn',
            'nama'           => 'required|string|max:100',
            'jenis_kelamin'  => 'required|in:L,P',
            'tempat_lahir'   => 'required|string|max:100',
            'tanggal_lahir'  => 'required|date',
            'alamat'         => 'required|string',
            'no_hp'          => 'nullable|string|max:20',
            'nama_orang_tua' => 'required|string|max:100',
            'id_kelas'       => 'required|exists:kelas,id_kelas',
            'id_tahun_ajaran'=> 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'status'         => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($request) {
            // Buat user akun untuk siswa
            $user = User::create([
                'nama'     => $request->nama,
                'username' => $request->nis,
                'password' => $request->nis,  // default password = NIS
                'status'   => $request->status,
            ]);
            $user->assignRole('siswa');

            $siswa = Siswa::create(array_merge(
                $request->only('nis', 'nisn', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_hp', 'nama_orang_tua', 'id_kelas', 'id_tahun_ajaran', 'status'),
                ['id_user' => $user->id]
            ));

            // Otomatis buat Virtual Account (prefix 88000 + NIS 5 digit terakhir)
            $nis = str_pad($request->nis, 5, '0', STR_PAD_LEFT);
            $nisLast5 = substr($nis, -5);
            $nomorVa = '88000' . $nisLast5;

            VirtualAccount::create([
                'id_siswa'      => $siswa->id_siswa,
                'nomor_va'      => $nomorVa,
                'prefix'        => '88000',
                'status'        => 'aktif',
                'tanggal_dibuat'=> now()->toDateString(),
            ]);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan. VA otomatis dibuat.');
    }

    public function show(Siswa $siswa)
    {
        $siswa->load('kelas.jurusan', 'tahunAjaran', 'virtualAccount', 'tagihan.pembayaran');
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas       = Kelas::with('jurusan')->orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::orderByDesc('tanggal_mulai')->get();
        return view('admin.siswa.edit', compact('siswa', 'kelas', 'tahunAjaran'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nis'            => 'required|string|max:20|unique:siswa,nis,' . $siswa->id_siswa . ',id_siswa',
            'nisn'           => 'required|string|max:20|unique:siswa,nisn,' . $siswa->id_siswa . ',id_siswa',
            'nama'           => 'required|string|max:100',
            'jenis_kelamin'  => 'required|in:L,P',
            'tempat_lahir'   => 'required|string|max:100',
            'tanggal_lahir'  => 'required|date',
            'alamat'         => 'required|string',
            'no_hp'          => 'nullable|string|max:20',
            'nama_orang_tua' => 'required|string|max:100',
            'id_kelas'       => 'required|exists:kelas,id_kelas',
            'id_tahun_ajaran'=> 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'status'         => 'required|in:aktif,nonaktif',
        ]);
        $siswa->update($request->only('nis', 'nisn', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_hp', 'nama_orang_tua', 'id_kelas', 'id_tahun_ajaran', 'status'));

        // Sync user account status
        if ($siswa->user) {
            $siswa->user->update(['nama' => $request->nama, 'status' => $request->status]);
        }
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa dihapus.');
    }
}
