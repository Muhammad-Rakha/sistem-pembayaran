<x-app-layout title="Instruksi Pembayaran">
    @php
        $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $response = $attempt->raw_response ?? [];
        $bank = strtoupper($attempt->bank ?? '');
    @endphp

    <div class="max-w-lg space-y-5">
        {{-- Success header --}}
        <div class="bg-blue-700 text-white rounded-xl p-6">
            <div class="flex items-center gap-3">
                <span class="text-4xl">🏦</span>
                <div>
                    <h2 class="text-xl font-bold">Tagihan Siap Dibayar</h2>
                    <p class="text-blue-200 text-sm mt-0.5">Bayar menggunakan transfer Virtual Account</p>
                </div>
            </div>
        </div>

        {{-- VA instructions --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Instruksi Pembayaran Virtual Account</h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Bank</span>
                    <span class="font-bold text-lg">{{ $bank }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">No. Virtual Account (Provider)</span>
                    <span class="font-mono font-bold text-2xl text-blue-700">{{ $attempt->nomor_va_provider ?? '—' }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Nominal</span>
                    <span class="font-bold text-xl">Rp {{ number_format($attempt->nominal,0,',','.') }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500">Order ID</span>
                    <span class="font-mono text-xs">{{ $attempt->order_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Batas Waktu</span>
                    <span class="text-red-600 font-semibold">
                        {{ $attempt->waktu_kedaluwarsa ? $attempt->waktu_kedaluwarsa->format('d/m/Y H:i') : '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Invoice info --}}
        <div class="bg-gray-50 rounded-xl p-4 text-sm">
            <p class="text-gray-500 mb-1">Tagihan untuk:</p>
            <p class="font-semibold">{{ $siswa->nama }} — SPP {{ $nb[$tagihan->bulan] }} {{ $tagihan->tahun }}</p>
        </div>

        {{-- Instructions --}}
        <div class="bg-white rounded-xl shadow p-6 text-sm">
            <h3 class="font-semibold text-gray-700 mb-3">Cara Membayar</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Buka aplikasi mobile banking atau ATM bank <strong>{{ $bank }}</strong>.</li>
                <li>Pilih menu <strong>Transfer → Virtual Account</strong>.</li>
                <li>Masukkan nomor VA: <strong class="font-mono">{{ $attempt->nomor_va_provider }}</strong></li>
                <li>Periksa nama & nominal lalu konfirmasi pembayaran.</li>
                <li>Setelah pembayaran berhasil, status tagihan akan diperbarui otomatis oleh sistem.</li>
                <li>Jangan menutup halaman ini sebelum pembayaran dikonfirmasi.</li>
            </ol>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('siswa.tagihan') }}" class="flex-1 text-center py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">
                ← Kembali ke Tagihan
            </a>
            <a href="{{ route('siswa.riwayat') }}" class="flex-1 text-center py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                Cek Riwayat Pembayaran
            </a>
        </div>

        {{-- After paying via simulator, webhook is instant. For Midtrans Sandbox
             without a public tunnel, status won't auto-update. Show the manual check. --}}
        <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4 text-sm text-yellow-800">
            <p class="font-semibold mb-1">⚠️ Status belum berubah setelah membayar?</p>
            <p>Jika Anda menggunakan <strong>Midtrans Sandbox</strong> di localhost tanpa tunnel (ngrok/cloudflared), notifikasi webhook tidak dapat diterima secara otomatis.</p>
            <p class="mt-2">Hubungi <strong>Administrator</strong> untuk memproses status secara manual melalui menu
                <strong>Cek Status Midtrans</strong>, atau gunakan tunnel sesuai petunjuk di bawah.</p>
            <details class="mt-3">
                <summary class="cursor-pointer font-medium">Cara setup ngrok (sekali pakai)</summary>
                <ol class="list-decimal list-inside mt-2 space-y-1 text-xs">
                    <li>Download ngrok dari <strong>ngrok.com</strong> dan jalankan: <code class="bg-white px-1 rounded">ngrok http 8000</code></li>
                    <li>Copy URL HTTPS yang muncul, contoh: <code class="bg-white px-1 rounded">https://xxxx.ngrok-free.app</code></li>
                    <li>Set di <code>.env</code>: <code class="bg-white px-1 rounded">MIDTRANS_NOTIFICATION_URL=https://xxxx.ngrok-free.app/webhooks/midtrans</code></li>
                    <li>Jalankan ulang: <code class="bg-white px-1 rounded">php artisan config:clear</code></li>
                </ol>
            </details>
        </div>
    </div>
</x-app-layout>
