<x-app-layout title="Detail Notifikasi">
    <div class="max-w-2xl space-y-5">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Detail Notifikasi Gateway</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">ID</dt><dd class="font-mono">{{ $notifikasi->id_notifikasi }}</dd></div>
                <div><dt class="text-gray-500">Waktu Diterima</dt><dd>{{ $notifikasi->created_at->format('d/m/Y H:i:s') }}</dd></div>
                <div><dt class="text-gray-500">Provider</dt><dd>
                    <span class="{{ $notifikasi->provider === 'midtrans' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }} text-xs px-2 py-0.5 rounded-full font-semibold">
                        {{ $notifikasi->provider }}
                    </span>
                </dd></div>
                <div><dt class="text-gray-500">Signature</dt><dd>
                    @if($notifikasi->signature_valid)
                        <span class="text-green-600 font-bold">✓ Valid</span>
                    @else
                        <span class="text-red-600 font-bold">✗ Invalid</span>
                    @endif
                </dd></div>
                <div><dt class="text-gray-500">Transaction ID</dt><dd class="font-mono text-xs">{{ $notifikasi->transaction_id ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Order ID</dt><dd class="font-mono text-xs">{{ $notifikasi->order_id ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Status Provider</dt><dd>{{ $notifikasi->status_provider ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Hasil Proses</dt><dd class="font-semibold">{{ $notifikasi->hasil_proses ?? '—' }}</dd></div>
                <div class="col-span-2"><dt class="text-gray-500">Pesan</dt><dd>{{ $notifikasi->pesan_proses ?? '—' }}</dd></div>
                @if($notifikasi->transaksi)
                <div class="col-span-2 border-t pt-3">
                    <dt class="text-gray-500 mb-1">Transaksi Terkait</dt>
                    <dd>
                        <a href="{{ route('admin.transaksi.show', $notifikasi->transaksi) }}" class="text-blue-600 hover:underline text-xs font-mono">
                            {{ $notifikasi->transaksi->transaction_id }} →
                        </a>
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-3">Raw Payload</h2>
            <pre class="bg-gray-50 rounded-lg p-4 text-xs overflow-x-auto">{{ json_encode($notifikasi->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</x-app-layout>
