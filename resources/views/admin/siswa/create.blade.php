<x-app-layout title="Tambah Siswa">
    <div class="max-w-2xl bg-white rounded-xl shadow p-6">
        <p class="text-sm text-blue-600 bg-blue-50 rounded-lg px-4 py-2 mb-4">
            Akun login dan Virtual Account akan dibuat otomatis. Username = NIS, password default = NIS.
        </p>
        <form method="POST" action="{{ route('admin.siswa.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="text" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required placeholder="Pilih tanggal lahir..."
                        class="datepicker w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="2" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">{{ old('alamat') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua</label>
                <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="id_kelas" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                                {{ $k->nama_kelas }} ({{ $k->jurusan->nama_jurusan ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                    <select name="id_tahun_ajaran" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($tahunAjaran as $ta)
                            <option value="{{ $ta->id_tahun_ajaran }}" {{ old('id_tahun_ajaran') == $ta->id_tahun_ajaran ? 'selected' : '' }}>{{ $ta->tahun_ajaran }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                <a href="{{ route('admin.siswa.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
