<x-app-layout title="Detail Transaksi">
    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Detail Transaksi</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Transaction ID</dt><dd class="font-mono font-bold">{{ $transaksi->transaction_id }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$transaksi->status_transaksi" /></dd></div>
                <div><dt class="text-gray-500">Virtual Account</dt><dd class="font-mono text-blue-700 font-bold">{{ $transaksi->virtual_account }}</dd></div>
                <div><dt class="text-gray-500">Siswa</dt><dd>{{ $transaksi->siswa->nama ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Nominal</dt><dd class="font-bold">Rp {{ number_format($transaksi->nominal,0,',','.') }}</dd></div>
                <div><dt class="text-gray-500">Tanggal/Waktu</dt><dd>{{ $transaksi->tanggal_transaksi }} {{ $transaksi->waktu_transaksi }}</dd></div>
                <div><dt class="text-gray-500">Reference No.</dt><dd class="font-mono text-xs">{{ $transaksi->reference_number ?? '-' }}</dd></div>
            </dl>
        </div>
        @if($transaksi->pembayaran)
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-3">Tagihan Dilunasi</h2>
            @php $nb = ['','Januari','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Bulan/Tahun</dt><dd>{{ $nb[$transaksi->pembayaran->tagihan->bulan] ?? '' }} {{ $transaksi->pembayaran->tagihan->tahun }}</dd></div>
                <div><dt class="text-gray-500">Tanggal Bayar</dt><dd>{{ $transaksi->pembayaran->tanggal_pembayaran->format('d/m/Y H:i') }}</dd></div>
            </dl>
        </div>
        @endif
    </div>
</x-app-layout>
