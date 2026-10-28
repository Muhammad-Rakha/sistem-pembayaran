<x-app-layout title="Cek Status Midtrans">
    <div class="max-w-2xl space-y-5">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Status Transaksi Midtrans</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Order ID</dt><dd class="font-mono text-xs">{{ $attempt->order_id }}</dd></div>
                <div><dt class="text-gray-500">Status Lokal</dt><dd><x-status-badge :status="$attempt->status" /></dd></div>
                <div><dt class="text-gray-500">Bank</dt><dd>{{ strtoupper($attempt->bank ?? '—') }}</dd></div>
                <div><dt class="text-gray-500">No. VA Provider</dt><dd class="font-mono text-blue-700 font-bold">{{ $attempt->nomor_va_provider ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Nominal</dt><dd>Rp {{ number_format($attempt->nominal,0,',','.') }}</dd></div>
                <div><dt class="text-gray-500">Siswa</dt><dd>{{ $attempt->tagihan->siswa->nama ?? '—' }}</dd></div>
            </dl>
        </div>

        @if($error)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm">
            <strong>Error mengambil status Midtrans:</strong> {{ $error }}
        </div>
        @endif

        @if($statusResponse)
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-3">Response Midtrans Transaction Status API</h3>
            <div class="flex items-center gap-3 mb-4">
                <span class="text-sm text-gray-500">transaction_status:</span>
                <span class="font-bold text-lg {{ ($statusResponse['transaction_status'] ?? '') === 'settlement' ? 'text-green-600' : 'text-yellow-600' }}">
                    {{ $statusResponse['transaction_status'] ?? '—' }}
                </span>
            </div>
            <pre class="bg-gray-50 rounded-lg p-4 text-xs overflow-x-auto max-h-60">{{ json_encode($statusResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

            @if(($statusResponse['transaction_status'] ?? '') === 'settlement')
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800">
                <strong>✅ Midtrans mengonfirmasi: settlement.</strong>
                Klik tombol di bawah untuk memproses pembayaran ini sekarang.
            </div>
            <form method="POST" action="{{ route('admin.midtrans.process', $attempt->order_id) }}" class="mt-3">
                @csrf
                <button class="w-full py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700">
                    ✅ Proses Pembayaran Sekarang
                </button>
            </form>
            @else
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                Status Midtrans bukan <strong>settlement</strong> — pembayaran belum dikonfirmasi oleh Midtrans.
            </div>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>
