<x-app-layout title="Detail Tagihan">
    @php $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Informasi Tagihan</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">ID Tagihan</dt><dd class="font-mono">{{ $tagihan->id_tagihan }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$tagihan->status" /></dd></div>
                <div><dt class="text-gray-500">NIS</dt><dd class="font-mono">{{ $tagihan->siswa->nis }}</dd></div>
                <div><dt class="text-gray-500">Nama Siswa</dt><dd class="font-semibold">{{ $tagihan->siswa->nama }}</dd></div>
                <div><dt class="text-gray-500">Kelas</dt><dd>{{ $tagihan->siswa->kelas->nama_kelas ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Virtual Account</dt><dd class="font-mono font-bold text-blue-700">{{ $tagihan->siswa->virtualAccount->nomor_va ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Bulan/Tahun</dt><dd>{{ $nb[$tagihan->bulan] }} {{ $tagihan->tahun }}</dd></div>
                <div><dt class="text-gray-500">Nominal</dt><dd class="font-bold text-lg">Rp {{ number_format($tagihan->nominal,0,',','.') }}</dd></div>
                <div><dt class="text-gray-500">Jatuh Tempo</dt><dd>{{ $tagihan->tanggal_jatuh_tempo->format('d F Y') }}</dd></div>
            </dl>
        </div>

        @if($tagihan->pembayaran)
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Pembayaran</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Tanggal Bayar</dt><dd class="font-semibold">{{ $tagihan->pembayaran->tanggal_pembayaran->format('d F Y H:i') }}</dd></div>
                <div><dt class="text-gray-500">Nominal Dibayar</dt><dd>Rp {{ number_format($tagihan->pembayaran->nominal_pembayaran,0,',','.') }}</dd></div>
                <div><dt class="text-gray-500">Transaction ID</dt><dd class="font-mono text-xs">{{ $tagihan->pembayaran->transaksi->transaction_id ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$tagihan->pembayaran->status" /></dd></div>
            </dl>
        </div>
        @else
        {{-- Show pending Midtrans attempts if any --}}
        @php
            $attempts = \App\Models\PermintaanPembayaran::where('id_tagihan', $tagihan->id_tagihan)
                ->where('provider', 'midtrans')->orderByDesc('created_at')->get();
        @endphp
        @if($attempts->count())
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-3">Permintaan Pembayaran Midtrans</h2>
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">Order ID</th><th class="pb-2 pr-4">Bank</th>
                    <th class="pb-2 pr-4">Status</th><th class="pb-2">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($attempts as $att)
                    <tr>
                        <td class="py-2 pr-4 font-mono text-xs">{{ $att->order_id }}</td>
                        <td class="py-2 pr-4">{{ strtoupper($att->bank ?? '—') }}</td>
                        <td class="py-2 pr-4"><x-status-badge :status="$att->status" /></td>
                        <td class="py-2">
                            <a href="{{ route('admin.midtrans.check', $att->order_id) }}"
                               class="text-xs text-blue-600 hover:underline">Cek Status →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="text-xs text-gray-400 mt-3">Klik "Cek Status" untuk mengambil status terbaru dari Midtrans dan memproses pembayaran jika sudah settlement.</p>
        </div>
        @endif
        @endif
    </div>
</x-app-layout>
