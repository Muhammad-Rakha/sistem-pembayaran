<x-app-layout title="Detail Siswa">
    <div class="max-w-3xl space-y-6">
        {{-- Profile --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-700">Profil Siswa</h2>
                <a href="{{ route('admin.siswa.edit', $siswa) }}" class="text-sm text-blue-600 hover:underline">Edit →</a>
            </div>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">NIS</dt><dd class="font-mono font-semibold">{{ $siswa->nis }}</dd></div>
                <div><dt class="text-gray-500">NISN</dt><dd class="font-mono">{{ $siswa->nisn }}</dd></div>
                <div><dt class="text-gray-500">Nama</dt><dd class="font-semibold">{{ $siswa->nama }}</dd></div>
                <div><dt class="text-gray-500">Jenis Kelamin</dt><dd>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                <div><dt class="text-gray-500">Tempat/Tgl Lahir</dt><dd>{{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d/m/Y') }}</dd></div>
                <div><dt class="text-gray-500">No. HP</dt><dd>{{ $siswa->no_hp ?? '-' }}</dd></div>
                <div class="col-span-2"><dt class="text-gray-500">Alamat</dt><dd>{{ $siswa->alamat }}</dd></div>
                <div><dt class="text-gray-500">Nama Orang Tua</dt><dd>{{ $siswa->nama_orang_tua }}</dd></div>
                <div><dt class="text-gray-500">Kelas</dt><dd>{{ $siswa->kelas->nama_kelas ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Tahun Ajaran</dt><dd>{{ $siswa->tahunAjaran->tahun_ajaran ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$siswa->status" /></dd></div>
                <div><dt class="text-gray-500">Virtual Account</dt><dd class="font-mono font-bold text-blue-700">{{ $siswa->virtualAccount->nomor_va ?? 'Belum ada' }}</dd></div>
            </dl>
        </div>

        {{-- Tagihan --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Riwayat Tagihan</h2>
            @php $namaBulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">Bulan/Tahun</th><th class="pb-2 pr-4">Nominal</th><th class="pb-2 pr-4">Jatuh Tempo</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Tgl Bayar</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($siswa->tagihan->sortByDesc('tahun')->sortByDesc('bulan') as $t)
                    <tr>
                        <td class="py-2 pr-4">{{ $namaBulan[$t->bulan] }} {{ $t->tahun }}</td>
                        <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="py-2 pr-4">{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                        <td class="py-2 pr-4"><x-status-badge :status="$t->status" /></td>
                        <td class="py-2">{{ $t->pembayaran ? $t->pembayaran->tanggal_pembayaran->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-4 text-center text-gray-400">Belum ada tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
