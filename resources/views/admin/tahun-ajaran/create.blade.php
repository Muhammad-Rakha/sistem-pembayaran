<x-app-layout title="Tambah Tahun Ajaran">
    <div class="max-w-lg bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.tahun-ajaran.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" placeholder="2026/2027" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="text" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required placeholder="Pilih tanggal mulai..."
                    class="datepicker w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                <input type="text" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required placeholder="Pilih tanggal selesai..."
                    class="datepicker w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                <a href="{{ route('admin.tahun-ajaran.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
