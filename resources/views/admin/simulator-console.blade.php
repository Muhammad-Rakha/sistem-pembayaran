<x-app-layout title="Simulator Console">
    <div class="max-w-3xl space-y-6">

        {{-- SIMULATION banner --}}
        <div class="bg-yellow-50 border-2 border-yellow-400 rounded-xl p-4 flex items-center gap-3">
            <span class="text-2xl">🧪</span>
            <div>
                <p class="font-bold text-yellow-800">SIMULASI — Bukan transaksi nyata</p>
                <p class="text-sm text-yellow-700">Console ini mengirim payload melalui webhook <code>/webhooks/simulator</code> lengkap dengan signature HMAC. Semua validasi dan pencatatan tetap berjalan seperti transaksi sesungguhnya.</p>
            </div>
        </div>

        {{-- Result from last send --}}
        @if(session('simulator_result'))
        @php $res = session('simulator_result'); @endphp
        <div class="{{ $res['response']['success'] ?? false ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' }} border rounded-xl p-4 text-sm">
            <p class="font-bold mb-2 {{ $res['response']['success'] ?? false ? 'text-green-700' : 'text-red-700' }}">
                {{ $res['response']['success'] ?? false ? '✅ Webhook berhasil diproses' : '❌ Webhook ditolak/gagal' }}
            </p>
            <p class="text-gray-600">{{ $res['response']['message'] ?? '-' }}</p>
            <details class="mt-2">
                <summary class="cursor-pointer text-gray-500 text-xs">Lihat payload & signature</summary>
                <pre class="mt-2 bg-white rounded p-3 text-xs overflow-x-auto">Payload: {{ json_encode($res['payload'], JSON_PRETTY_PRINT) }}

Signature (HMAC-SHA256): {{ $res['signature'] }}

Response: {{ json_encode($res['response'], JSON_PRETTY_PRINT) }}</pre>
            </details>
        </div>
        @endif

        {{-- Send form --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-5">Kirim Notifikasi Simulasi</h2>
            <form method="POST" action="{{ route('admin.simulator.console.kirim') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Virtual Account (Sekolah)</label>
                        <select name="virtual_account" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            @foreach($siswaList as $s)
                                @if($s->virtualAccount)
                                <option value="{{ $s->virtualAccount->nomor_va }}">
                                    {{ $s->nis }} – {{ $s->nama }} ({{ $s->virtualAccount->nomor_va }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" value="350000" min="1"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
                        <input type="text" name="transaction_id"
                            value="TRX-{{ now()->format('Ymd') }}-{{ strtoupper(substr(md5(uniqid()), 0, 6)) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="PAID">PAID (berhasil)</option>
                            <option value="FAILED">FAILED (gagal)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Waktu Pembayaran <span class="text-gray-400 text-xs">(paid_at — untuk skenario tanggal §11)</span>
                    </label>
                    <input type="text" name="paid_at" value="{{ now()->format('Y-m-d H:i:s') }}" placeholder="Pilih waktu pembayaran..."
                        class="datetimepicker w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500 bg-white cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">
                        Contoh PRD: 25 Sep → 2026-09-25 10:15:00 | 30 Sep → 2026-09-30 23:55:00 | 5 Okt → 2026-10-05 09:00:00
                    </p>
                </div>

                <button type="submit" class="w-full py-3 bg-yellow-500 text-white rounded-xl font-bold hover:bg-yellow-600">
                    🚀 Kirim Webhook Simulasi
                </button>
            </form>
        </div>

        {{-- Tagihan yang bisa dibayar --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-3">Tagihan UNPAID / OVERDUE (referensi cepat)</h3>
            @php $nb = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
            <table class="w-full text-xs">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-1 pr-3">NIS</th><th class="pb-1 pr-3">Nama</th><th class="pb-1 pr-3">VA</th>
                    <th class="pb-1 pr-3">Bulan</th><th class="pb-1 pr-3">Nominal</th><th class="pb-1">Status</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tagihanList as $t)
                    <tr>
                        <td class="py-1 pr-3 font-mono">{{ $t->siswa->nis ?? '-' }}</td>
                        <td class="py-1 pr-3">{{ $t->siswa->nama ?? '-' }}</td>
                        <td class="py-1 pr-3 font-mono text-blue-700">{{ $t->siswa->virtualAccount->nomor_va ?? '-' }}</td>
                        <td class="py-1 pr-3">{{ $nb[$t->bulan] }} {{ $t->tahun }}</td>
                        <td class="py-1 pr-3">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="py-1"><x-status-badge :status="$t->status" /></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-3 text-center text-gray-400">Tidak ada tagihan UNPAID/OVERDUE.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
