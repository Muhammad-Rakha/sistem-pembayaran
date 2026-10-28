<x-app-layout title="Pembayaran">
    <div class="bg-white rounded-xl shadow p-6">
        <form method="GET" class="flex gap-2 mb-4">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari NIS / Nama siswa..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 outline-none focus:ring-2 focus:ring-blue-300">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">Tanggal Bayar</th><th class="pb-2 pr-4">NIS</th><th class="pb-2 pr-4">Nama</th>
                    <th class="pb-2 pr-4">Kelas</th><th class="pb-2 pr-4">Bulan/Tahun</th><th class="pb-2 pr-4">Nominal</th>
                    <th class="pb-2 pr-4">Transaction ID</th><th class="pb-2">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pembayaran as $p)
                    @php $nb = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
                    <tr>
                        <td class="py-2 pr-4">{{ $p->tanggal_pembayaran->format('d/m/Y H:i') }}</td>
                        <td class="py-2 pr-4 font-mono">{{ $p->tagihan->siswa->nis ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ $p->tagihan->siswa->nama ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ $p->tagihan->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ $nb[$p->tagihan->bulan] ?? '-' }} {{ $p->tagihan->tahun }}</td>
                        <td class="py-2 pr-4">Rp {{ number_format($p->nominal_pembayaran,0,',','.') }}</td>
                        <td class="py-2 pr-4 font-mono text-xs">{{ $p->transaksi->transaction_id ?? '-' }}</td>
                        <td class="py-2"><x-status-badge :status="$p->status" /></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-6 text-center text-gray-400">Belum ada pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$pembayaran" />
    </div>
</x-app-layout>
