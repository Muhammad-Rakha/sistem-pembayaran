<x-app-layout title="Edit Siswa">
    <div class="max-w-2xl bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis', $siswa->nis) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                    <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama', $siswa->nama) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="L" {{ $siswa->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $siswa->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="text" name="tanggal_lahir" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir?->format('Y-m-d')) }}" required placeholder="Pilih tanggal lahir..."
                        class="datepicker w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" rows="2" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua</label>
                <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua', $siswa->nama_orang_tua) }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="id_kelas" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}" {{ $siswa->id_kelas == $k->id_kelas ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                    <select name="id_tahun_ajaran" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach($tahunAjaran as $ta)
                            <option value="{{ $ta->id_tahun_ajaran }}" {{ $siswa->id_tahun_ajaran == $ta->id_tahun_ajaran ? 'selected' : '' }}>{{ $ta->tahun_ajaran }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="aktif" {{ $siswa->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $siswa->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                <a href="{{ route('admin.siswa.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
