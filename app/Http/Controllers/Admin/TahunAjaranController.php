<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $list = TahunAjaran::orderByDesc('tanggal_mulai')->paginate(15);
        return view('admin.tahun-ajaran.index', compact('list'));
    }

    public function create()
    {
        return view('admin.tahun-ajaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran'    => 'required|string|max:20|unique:tahun_ajaran,tahun_ajaran',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status'          => 'required|in:aktif,nonaktif',
        ]);
        TahunAjaran::create($request->only('tahun_ajaran', 'tanggal_mulai', 'tanggal_selesai', 'status'));
        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'tahun_ajaran'    => 'required|string|max:20|unique:tahun_ajaran,tahun_ajaran,' . $tahunAjaran->id_tahun_ajaran . ',id_tahun_ajaran',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status'          => 'required|in:aktif,nonaktif',
        ]);
        $tahunAjaran->update($request->only('tahun_ajaran', 'tanggal_mulai', 'tanggal_selesai', 'status'));
        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran dihapus.');
    }
}
