<x-app-layout title="Profil Saya">
    @php
        $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $tanggalLahir = \Carbon\Carbon::parse($siswa->tanggal_lahir);
        $jenisKelamin = $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    @endphp

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Left column: identity and editable contact data --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Profile header --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl p-6 text-white shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center text-2xl font-bold shrink-0">
                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-xl font-bold truncate">{{ $siswa->nama }}</h2>
                        <p class="text-emerald-100 text-sm mt-0.5">
                            NIS <span class="font-mono font-semibold">{{ $siswa->nis }}</span>
                            &middot; {{ $siswa->kelas->nama_kelas ?? '-' }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <x-status-badge :status="$siswa->status" />
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/15 border border-white/20">
                                {{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Default password warning --}}
            @if($isDefaultPassword)
                <div class="px-4 py-3.5 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86l-8.58 14.86A1 1 0 002.58 20h18.84a1 1 0 00.87-1.28l-8.58-14.86a1 1 0 00-1.72 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold">Anda masih menggunakan password default (NIS).</p>
                        <p class="mt-0.5 text-amber-700">Ubah password melalui formulir di samping agar akun Anda lebih aman.</p>
                    </div>
                </div>
            @endif

            {{-- Personal data (read-only, automatically taken from siswa) --}}
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-semibold text-slate-800">Data Pribadi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Data ini otomatis diambil dari data siswa yang terhubung dengan akun Anda.</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500">NIS</dt>
                        <dd class="font-mono font-semibold text-slate-800 mt-0.5">{{ $siswa->nis }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">NISN</dt>
                        <dd class="font-mono text-slate-800 mt-0.5">{{ $siswa->nisn }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Nama Lengkap</dt>
                        <dd class="font-semibold text-slate-800 mt-0.5">{{ $siswa->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Jenis Kelamin</dt>
                        <dd class="text-slate-800 mt-0.5">{{ $jenisKelamin }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Tempat, Tanggal Lahir</dt>
                        <dd class="text-slate-800 mt-0.5">
                            {{ $siswa->tempat_lahir }}, {{ $tanggalLahir->format('d') }} {{ $namaBulan[$tanggalLahir->month] }} {{ $tanggalLahir->year }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Nama Orang Tua</dt>
                        <dd class="text-slate-800 mt-0.5">{{ $siswa->nama_orang_tua }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">No. HP</dt>
                        <dd class="text-slate-800 mt-0.5">{{ $siswa->no_hp ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Status Siswa</dt>
                        <dd class="mt-0.5"><x-status-badge :status="$siswa->status" /></dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-slate-500">Alamat</dt>
                        <dd class="text-slate-800 mt-0.5 leading-relaxed">{{ $siswa->alamat }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Editable contact data --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-slate-800">Perbarui Data Kontak</h3>
                <p class="text-xs text-slate-500 mt-0.5 mb-5">
                    Anda hanya dapat mengubah nomor HP dan alamat. Data identitas dikelola oleh administrator sekolah.
                </p>

                <form method="POST" action="{{ route('siswa.profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
                        <input id="no_hp"
                               type="text"
                               name="no_hp"
                               value="{{ old('no_hp', $siswa->no_hp) }}"
                               placeholder="Contoh: 081234567890"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                        <x-input-error :messages="$errors->updateProfile->get('no_hp')" class="mt-1.5" />
                    </div>

                    <div>
                        <label for="alamat" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                        <textarea id="alamat"
                                  name="alamat"
                                  rows="3"
                                  required
                                  class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">{{ old('alamat', $siswa->alamat) }}</textarea>
                        <x-input-error :messages="$errors->updateProfile->get('alamat')" class="mt-1.5" />
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="px-5 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right column: academic, account, and password --}}
        <div class="space-y-6">

            {{-- Academic data --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Data Akademik</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-500">Kelas</dt>
                        <dd class="font-semibold text-slate-800 text-right">{{ $siswa->kelas->nama_kelas ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-500">Tingkat</dt>
                        <dd class="font-semibold text-slate-800 text-right">{{ $siswa->kelas->tingkat ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-500">Jurusan</dt>
                        <dd class="font-semibold text-slate-800 text-right">{{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-500">Kode Jurusan</dt>
                        <dd class="font-mono font-semibold text-slate-800 text-right">{{ $siswa->kelas->jurusan->kode_jurusan ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-500">Tahun Ajaran</dt>
                        <dd class="font-semibold text-slate-800 text-right">{{ $siswa->tahunAjaran->tahun_ajaran ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Account data --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Informasi Akun</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-500">Username</dt>
                        <dd class="font-mono font-semibold text-slate-800 text-right">{{ $user->username }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-500">Status Akun</dt>
                        <dd class="mt-0.5"><x-status-badge :status="$user->status" /></dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-500">Virtual Account</dt>
                        <dd class="font-mono font-semibold text-emerald-700 text-right">
                            {{ $siswa->virtualAccount->nomor_va ?? 'Belum ada' }}
                        </dd>
                    </div>
                </dl>
                <p class="mt-4 pt-4 border-t border-slate-100 text-[11px] text-slate-400 leading-relaxed">
                    Username mengikuti NIS dan tidak dapat diubah. Hubungi administrator jika data identitas perlu diperbaiki.
                </p>
            </div>

            {{-- Change password --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-slate-800">Ubah Password</h3>
                <p class="text-xs text-slate-500 mt-0.5 mb-4">
                    Password awal Anda adalah NIS. Gunakan minimal 8 karakter untuk password baru.
                </p>

                <form method="POST" action="{{ route('siswa.profile.password') }}" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1">Password Saat Ini</label>
                        <input id="current_password"
                               type="password"
                               name="current_password"
                               required
                               autocomplete="current-password"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                        <input id="password"
                               type="password"
                               name="password"
                               required
                               autocomplete="new-password"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                        <input id="password_confirmation"
                               type="password"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-300 focus:border-emerald-400">
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-900 transition">
                        Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
