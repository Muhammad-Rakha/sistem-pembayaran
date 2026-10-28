<x-app-layout title="Cari Siswa / VA / Transaksi">
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Masukkan NIS / NISN / Nama / No. VA / Transaction ID..."
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300"
                autofocus>
            <button class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">🔍 Cari</button>
        </form>
    </div>

    @if($result)
        {{-- Siswa results --}}
        @if($result['siswa']->count() > 0)
        <div class="bg-white rounded-xl shadow p-6 mb-4">
            <h3 class="font-semibold text-gray-700 mb-3">Hasil Siswa ({{ $result['siswa']->count() }})</h3>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">NIS</th><th class="pb-2 pr-4">NISN</th><th class="pb-2 pr-4">Nama</th>
                    <th class="pb-2 pr-4">Kelas</th><th class="pb-2 pr-4">No. VA</th><th class="pb-2">Status Tagihan</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($result['siswa'] as $s)
                    <tr>
                        <td class="py-2 pr-4 font-mono">{{ $s->nis }}</td>
                        <td class="py-2 pr-4 font-mono">{{ $s->nisn }}</td>
                        <td class="py-2 pr-4 font-semibold">{{ $s->nama }}</td>
                        <td class="py-2 pr-4">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                        <td class="py-2 pr-4 font-mono font-bold text-blue-700">{{ $s->virtualAccount->nomor_va ?? '-' }}</td>
                        <td class="py-2">
                            @php
                                $tagihanBulanIni = $s->tagihan->where('bulan', now()->month)->where('tahun', now()->year)->first();
                            @endphp
                            @if($tagihanBulanIni)
                                <x-status-badge :status="$tagihanBulanIni->status" />
                            @else
                                <span class="text-xs text-gray-400">Belum ada tagihan bln ini</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Transaksi results --}}
        @if($result['transaksi']->count() > 0)
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-3">Hasil Transaksi ({{ $result['transaksi']->count() }})</h3>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">Transaction ID</th><th class="pb-2 pr-4">VA</th><th class="pb-2 pr-4">Siswa</th>
                    <th class="pb-2 pr-4">Nominal</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Waktu</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($result['transaksi'] as $t)
                    <tr>
                        <td class="py-2 pr-4 font-mono text-xs">{{ $t->transaction_id }}</td>
                        <td class="py-2 pr-4 font-mono text-xs">{{ $t->virtual_account }}</td>
                        <td class="py-2 pr-4">{{ $t->siswa->nama ?? '-' }}</td>
                        <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="py-2 pr-4"><x-status-badge :status="$t->status_transaksi" /></td>
                        <td class="py-2 text-xs text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($result['siswa']->count() === 0 && $result['transaksi']->count() === 0)
        <div class="bg-white rounded-xl shadow p-6 text-center text-gray-400">
            Tidak ditemukan data untuk "<strong>{{ $q }}</strong>".
        </div>
        @endif
    @endif
</x-app-layout>
