<x-app-layout title="Detail Transaksi">
    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Detail Transaksi Payment Gateway</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Transaction ID</dt><dd class="font-mono font-bold">{{ $transaksi->transaction_id }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$transaksi->status_transaksi" /></dd></div>
                <div><dt class="text-gray-500">Virtual Account</dt><dd class="font-mono text-blue-700 font-bold">{{ $transaksi->virtual_account }}</dd></div>
                <div><dt class="text-gray-500">Siswa</dt><dd>{{ $transaksi->siswa->nama ?? '-' }} ({{ $transaksi->siswa->nis ?? '' }})</dd></div>
                <div><dt class="text-gray-500">Nominal</dt><dd class="font-bold text-lg">Rp {{ number_format($transaksi->nominal,0,',','.') }}</dd></div>
                <div><dt class="text-gray-500">Channel</dt><dd>{{ $transaksi->payment_channel ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Tanggal Transaksi</dt><dd>{{ $transaksi->tanggal_transaksi }} {{ $transaksi->waktu_transaksi }}</dd></div>
                <div><dt class="text-gray-500">Reference No.</dt><dd class="font-mono text-xs">{{ $transaksi->reference_number ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Diterima Pada</dt><dd class="text-xs">{{ $transaksi->created_at->format('d/m/Y H:i:s') }}</dd></div>
            </dl>
        </div>

        @if($transaksi->pembayaran)
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-3">Tagihan yang Dilunasi</h2>
            @php $nb = ['','Januari','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Bulan/Tahun</dt><dd>{{ $nb[$transaksi->pembayaran->tagihan->bulan] ?? '' }} {{ $transaksi->pembayaran->tagihan->tahun }}</dd></div>
                <div><dt class="text-gray-500">Nominal Tagihan</dt><dd>Rp {{ number_format($transaksi->pembayaran->tagihan->nominal,0,',','.') }}</dd></div>
                <div><dt class="text-gray-500">Tanggal Bayar</dt><dd>{{ $transaksi->pembayaran->tanggal_pembayaran->format('d/m/Y H:i') }}</dd></div>
            </dl>
        </div>
        @endif

        @if($transaksi->raw_response)
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-3">Raw Callback Payload</h2>
            <pre class="bg-gray-50 rounded-lg p-4 text-xs overflow-x-auto">{{ json_encode($transaksi->raw_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
    </div>
</x-app-layout>
