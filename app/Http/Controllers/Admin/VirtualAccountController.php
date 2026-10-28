<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VirtualAccount;
use App\Models\Siswa;
use Illuminate\Http\Request;

class VirtualAccountController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $va = VirtualAccount::with('siswa.kelas')
            ->when($q, fn ($query) => $query
                ->where('nomor_va', 'like', "%$q%")
                ->orWhereHas('siswa', fn ($s) => $s
                    ->where('nama', 'like', "%$q%")
                    ->orWhere('nis', 'like', "%$q%")
                )
            )
            ->orderBy('nomor_va')
            ->paginate(15)
            ->withQueryString();
        return view('admin.virtual-account.index', compact('va', 'q'));
    }

    public function create()
    {
        // Only students who don't have a VA yet
        $siswa = Siswa::doesntHave('virtualAccount')->orderBy('nama')->get();
        return view('admin.virtual-account.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id_siswa|unique:virtual_account,id_siswa',
            'prefix'   => 'required|string|max:10',
        ]);

        $siswa = Siswa::findOrFail($request->id_siswa);
        $nisLast5 = substr(str_pad($siswa->nis, 5, '0', STR_PAD_LEFT), -5);
        $nomorVa  = $request->prefix . $nisLast5;

        // Validate uniqueness
        if (VirtualAccount::where('nomor_va', $nomorVa)->exists()) {
            return back()->withErrors(['nomor_va' => 'Nomor VA sudah digunakan. Ganti prefix.']);
        }

        VirtualAccount::create([
            'id_siswa'      => $siswa->id_siswa,
            'nomor_va'      => $nomorVa,
            'prefix'        => $request->prefix,
            'status'        => 'aktif',
            'tanggal_dibuat'=> now()->toDateString(),
        ]);
        return redirect()->route('admin.virtual-account.index')->with('success', 'Virtual Account berhasil dibuat.');
    }

    public function show(VirtualAccount $virtualAccount)
    {
        $virtualAccount->load('siswa.kelas.jurusan', 'siswa.tagihan.pembayaran');
        return view('admin.virtual-account.show', compact('virtualAccount'));
    }

    public function edit(VirtualAccount $virtualAccount)
    {
        return view('admin.virtual-account.edit', compact('virtualAccount'));
    }

    public function update(Request $request, VirtualAccount $virtualAccount)
    {
        $request->validate([
            'status' => 'required|in:aktif,nonaktif',
        ]);
        $virtualAccount->update(['status' => $request->status]);
        return redirect()->route('admin.virtual-account.index')->with('success', 'Status VA diperbarui.');
    }

    public function destroy(VirtualAccount $virtualAccount)
    {
        $virtualAccount->delete();
        return redirect()->route('admin.virtual-account.index')->with('success', 'Virtual Account dihapus.');
    }
}
