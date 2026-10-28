<x-app-layout title="Tambah Virtual Account">
    <div class="max-w-lg bg-white rounded-xl shadow p-6">
        <p class="text-sm text-blue-600 bg-blue-50 rounded-lg px-4 py-2 mb-4">
            Nomor VA = Prefix + 5 digit terakhir NIS. Contoh: NIS 12345 + Prefix 88000 → VA 8800012345
        </p>
        <form method="POST" action="{{ route('admin.virtual-account.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Siswa (tanpa VA)</label>
                <select name="id_siswa" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswa as $s)
                        <option value="{{ $s->id_siswa }}" {{ old('id_siswa') == $s->id_siswa ? 'selected' : '' }}>
                            {{ $s->nis }} — {{ $s->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prefix VA</label>
                <input type="text" name="prefix" value="{{ old('prefix', '88000') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                <p class="text-xs text-gray-400 mt-1">5 digit terakhir NIS akan ditambahkan otomatis.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Buat VA</button>
                <a href="{{ route('admin.virtual-account.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
