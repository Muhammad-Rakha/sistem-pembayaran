<x-app-layout title="Tambah Tarif SPP">
    <div class="max-w-lg bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.tarif-spp.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                <select name="id_tahun_ajaran" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach($tahunAjaran as $ta)
                        <option value="{{ $ta->id_tahun_ajaran }}" {{ old('id_tahun_ajaran') == $ta->id_tahun_ajaran ? 'selected' : '' }}>{{ $ta->tahun_ajaran }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                <input type="number" name="nominal" value="{{ old('nominal') }}" required min="1"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                <a href="{{ route('admin.tarif-spp.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
