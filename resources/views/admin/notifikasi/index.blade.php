<x-app-layout title="Log Notifikasi Gateway">
    <div class="bg-white rounded-xl shadow p-6">
        <form method="GET" class="flex flex-wrap gap-2 mb-4">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari Transaction ID / Order ID..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72 outline-none focus:ring-2 focus:ring-blue-300">
            <select name="provider" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Provider</option>
                <option value="midtrans"  {{ $provider === 'midtrans'  ? 'selected' : '' }}>Midtrans</option>
                <option value="simulator" {{ $provider === 'simulator' ? 'selected' : '' }}>Simulator</option>
            </select>
            <select name="hasil" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Hasil</option>
                <option value="processed"              {{ $hasil === 'processed' ? 'selected' : '' }}>Processed</option>
                <option value="duplicate"              {{ $hasil === 'duplicate' ? 'selected' : '' }}>Duplicate</option>
                <option value="rejected-signature"     {{ $hasil === 'rejected-signature' ? 'selected' : '' }}>Invalid Signature</option>
                <option value="rejected-amount-mismatch" {{ $hasil === 'rejected-amount-mismatch' ? 'selected' : '' }}>Amount Mismatch</option>
                <option value="rejected-unknown-va"    {{ $hasil === 'rejected-unknown-va' ? 'selected' : '' }}>Unknown VA</option>
                <option value="not-success"            {{ $hasil === 'not-success' ? 'selected' : '' }}>Not Success</option>
            </select>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-3">Waktu</th>
                    <th class="pb-2 pr-3">Provider</th>
                    <th class="pb-2 pr-3">Transaction ID</th>
                    <th class="pb-2 pr-3">Order ID</th>
                    <th class="pb-2 pr-3">Signature</th>
                    <th class="pb-2 pr-3">Status Provider</th>
                    <th class="pb-2 pr-3">Hasil</th>
                    <th class="pb-2">Detail</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($notifikasi as $n)
                    <tr class="{{ $n->hasil_proses === 'processed' ? 'bg-green-50' : ($n->signature_valid ? '' : 'bg-red-50') }}">
                        <td class="py-2 pr-3 text-xs text-gray-500">{{ $n->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="py-2 pr-3">
                            <span class="{{ $n->provider === 'midtrans' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }} text-xs px-2 py-0.5 rounded-full font-semibold">
                                {{ $n->provider }}
                            </span>
                        </td>
                        <td class="py-2 pr-3 font-mono text-xs">{{ $n->transaction_id ?? '—' }}</td>
                        <td class="py-2 pr-3 font-mono text-xs">{{ $n->order_id ?? '—' }}</td>
                        <td class="py-2 pr-3">
                            @if($n->signature_valid)
                                <span class="text-green-600 font-bold text-xs">✓ Valid</span>
                            @else
                                <span class="text-red-600 font-bold text-xs">✗ Invalid</span>
                            @endif
                        </td>
                        <td class="py-2 pr-3 text-xs">{{ $n->status_provider ?? '—' }}</td>
                        <td class="py-2 pr-3">
                            @php
                                $hasilMap = [
                                    'processed'             => 'bg-green-100 text-green-700',
                                    'duplicate'             => 'bg-gray-100 text-gray-600',
                                    'rejected-signature'    => 'bg-red-100 text-red-700',
                                    'rejected-amount-mismatch' => 'bg-orange-100 text-orange-700',
                                    'rejected-unknown-va'   => 'bg-orange-100 text-orange-700',
                                    'not-success'           => 'bg-yellow-100 text-yellow-700',
                                ];
                                $hasilCls = $hasilMap[$n->hasil_proses] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $hasilCls }}">{{ $n->hasil_proses ?? '—' }}</span>
                        </td>
                        <td class="py-2">
                            <a href="{{ route('admin.notifikasi.show', $n) }}" class="text-blue-600 text-xs hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-6 text-center text-gray-400">Belum ada notifikasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$notifikasi" />
    </div>
</x-app-layout>
