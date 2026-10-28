<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $jurusan = Jurusan::when($q, fn ($query) =>
            $query->where('nama_jurusan', 'like', "%$q%")->orWhere('kode_jurusan', 'like', "%$q%")
        )->orderBy('nama_jurusan')->paginate(15)->withQueryString();
        return view('admin.jurusan.index', compact('jurusan', 'q'));
    }

    public function create()
    {
        return view('admin.jurusan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan'  => 'required|string|max:20|unique:jurusan,kode_jurusan',
            'nama_jurusan'  => 'required|string|max:100',
            'keterangan'    => 'nullable|string',
        ]);
        Jurusan::create($request->only('kode_jurusan', 'nama_jurusan', 'keterangan'));
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'kode_jurusan'  => 'required|string|max:20|unique:jurusan,kode_jurusan,' . $jurusan->id_jurusan . ',id_jurusan',
            'nama_jurusan'  => 'required|string|max:100',
            'keterangan'    => 'nullable|string',
        ]);
        $jurusan->update($request->only('kode_jurusan', 'nama_jurusan', 'keterangan'));
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan dihapus.');
    }
}
