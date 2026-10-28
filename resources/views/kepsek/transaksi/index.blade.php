<x-app-layout title="Transaksi">
    <div class="bg-white rounded-xl shadow p-6">
        <form method="GET" class="flex gap-2 mb-4">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari Transaction ID / VA / Nama..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-80 outline-none">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
        </form>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">Transaction ID</th><th class="pb-2 pr-4">VA</th><th class="pb-2 pr-4">Siswa</th>
                <th class="pb-2 pr-4">Nominal</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Waktu</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transaksi as $t)
                <tr>
                    <td class="py-2 pr-4 font-mono text-xs">{{ $t->transaction_id }}</td>
                    <td class="py-2 pr-4 font-mono text-xs">{{ $t->virtual_account }}</td>
                    <td class="py-2 pr-4">{{ $t->siswa->nama ?? '-' }}</td>
                    <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                    <td class="py-2 pr-4"><x-status-badge :status="$t->status_transaksi" /></td>
                    <td class="py-2 text-xs text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-6 text-center text-gray-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$transaksi" />
    </div>
</x-app-layout>
