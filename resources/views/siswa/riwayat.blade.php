<x-app-layout title="Riwayat Pembayaran">
    @php $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">Riwayat Pembayaran</h2>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">Tanggal Bayar</th><th class="pb-2 pr-4">Bulan/Tahun</th>
                <th class="pb-2 pr-4">Nominal</th><th class="pb-2 pr-4">Transaction ID</th><th class="pb-2">Status</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($riwayat as $r)
                <tr>
                    <td class="py-2 pr-4">{{ $r->tanggal_pembayaran->format('d/m/Y H:i') }}</td>
                    <td class="py-2 pr-4">{{ $nb[$r->tagihan->bulan] ?? '' }} {{ $r->tagihan->tahun }}</td>
                    <td class="py-2 pr-4">Rp {{ number_format($r->nominal_pembayaran,0,',','.') }}</td>
                    <td class="py-2 pr-4 font-mono text-xs">{{ $r->transaksi->transaction_id ?? '-' }}</td>
                    <td class="py-2"><x-status-badge :status="$r->status" /></td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada riwayat pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$riwayat" />
    </div>
</x-app-layout>
