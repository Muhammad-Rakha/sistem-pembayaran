<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TarifSpp;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TarifSppController extends Controller
{
    public function index()
    {
        $tarif = TarifSpp::with('tahunAjaran')->orderByDesc('created_at')->paginate(15);
        return view('admin.tarif-spp.index', compact('tarif'));
    }

    public function create()
    {
        $tahunAjaran = TahunAjaran::orderByDesc('tanggal_mulai')->get();
        return view('admin.tarif-spp.create', compact('tahunAjaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'nominal'         => 'required|integer|min:1',
            'keterangan'      => 'nullable|string',
            'status'          => 'required|in:aktif,nonaktif',
        ]);
        TarifSpp::create($request->only('id_tahun_ajaran', 'nominal', 'keterangan', 'status'));
        return redirect()->route('admin.tarif-spp.index')->with('success', 'Tarif SPP berhasil ditambahkan.');
    }

    public function edit(TarifSpp $tarifSpp)
    {
        $tahunAjaran = TahunAjaran::orderByDesc('tanggal_mulai')->get();
        return view('admin.tarif-spp.edit', compact('tarifSpp', 'tahunAjaran'));
    }

    public function update(Request $request, TarifSpp $tarifSpp)
    {
        $request->validate([
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id_tahun_ajaran',
            'nominal'         => 'required|integer|min:1',
            'keterangan'      => 'nullable|string',
            'status'          => 'required|in:aktif,nonaktif',
        ]);
        $tarifSpp->update($request->only('id_tahun_ajaran', 'nominal', 'keterangan', 'status'));
        return redirect()->route('admin.tarif-spp.index')->with('success', 'Tarif SPP berhasil diperbarui.');
    }

    public function destroy(TarifSpp $tarifSpp)
    {
        $tarifSpp->delete();
        return redirect()->route('admin.tarif-spp.index')->with('success', 'Tarif SPP dihapus.');
    }
}
