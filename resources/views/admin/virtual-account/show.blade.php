<x-app-layout title="Detail Virtual Account">
    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Detail Virtual Account</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">No. VA</dt><dd class="font-mono font-bold text-2xl text-blue-700">{{ $virtualAccount->nomor_va }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$virtualAccount->status" /></dd></div>
                <div><dt class="text-gray-500">NIS</dt><dd class="font-mono">{{ $virtualAccount->siswa->nis }}</dd></div>
                <div><dt class="text-gray-500">Nama Siswa</dt><dd class="font-semibold">{{ $virtualAccount->siswa->nama }}</dd></div>
                <div><dt class="text-gray-500">Kelas</dt><dd>{{ $virtualAccount->siswa->kelas->nama_kelas ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Jurusan</dt><dd>{{ $virtualAccount->siswa->kelas->jurusan->nama_jurusan ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Tanggal Dibuat</dt><dd>{{ $virtualAccount->tanggal_dibuat }}</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Tagihan Siswa Ini</h2>
            @php $nb = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">Bulan/Tahun</th><th class="pb-2 pr-4">Nominal</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Tgl Bayar</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($virtualAccount->siswa->tagihan->sortByDesc('tahun') as $t)
                    <tr>
                        <td class="py-2 pr-4">{{ $nb[$t->bulan] }} {{ $t->tahun }}</td>
                        <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="py-2 pr-4"><x-status-badge :status="$t->status" /></td>
                        <td class="py-2">{{ $t->pembayaran ? $t->pembayaran->tanggal_pembayaran->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-400">Belum ada tagihan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
