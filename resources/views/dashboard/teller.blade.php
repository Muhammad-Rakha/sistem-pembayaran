<x-app-layout title="Dashboard Teller">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <x-stat-card label="Transaksi Hari Ini" :value="$transaksiHariIni" color="blue" icon="🔄" />
        <x-stat-card label="Nominal Hari Ini" :value="'Rp '.number_format($nominalHariIni,0,',','.')" color="green" icon="💰" />
    </div>

    {{-- Quick search --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h2 class="font-semibold text-gray-700 mb-3">Cari Cepat</h2>
        <form action="{{ route('teller.cari') }}" method="GET" class="flex gap-2">
            <input type="text" name="q" placeholder="NIS / NISN / Nama / No. VA / Transaction ID"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300 outline-none">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Cari</button>
        </form>
    </div>

    {{-- Recent transactions --}}
    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-4">Transaksi Terbaru</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2 pr-4">Transaction ID</th>
                        <th class="pb-2 pr-4">VA</th>
                        <th class="pb-2 pr-4">Siswa</th>
                        <th class="pb-2 pr-4">Nominal</th>
                        <th class="pb-2 pr-4">Status</th>
                        <th class="pb-2">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentTransaksi as $trx)
                    <tr>
                        <td class="py-2 pr-4 font-mono text-xs">{{ $trx->transaction_id }}</td>
                        <td class="py-2 pr-4 font-mono text-xs">{{ $trx->virtual_account }}</td>
                        <td class="py-2 pr-4">{{ $trx->siswa->nama ?? '-' }}</td>
                        <td class="py-2 pr-4">Rp {{ number_format($trx->nominal,0,',','.') }}</td>
                        <td class="py-2 pr-4"><x-status-badge :status="$trx->status_transaksi" /></td>
                        <td class="py-2 text-xs text-gray-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-6 text-center text-gray-400">Belum ada transaksi hari ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
