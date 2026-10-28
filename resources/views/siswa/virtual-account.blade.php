<x-app-layout title="Virtual Account Saya">
    <div class="max-w-md bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">Nomor Virtual Account Anda</h2>
        @if($siswa->virtualAccount)
        <div class="bg-blue-700 text-white rounded-xl p-6 text-center">
            <p class="text-sm text-blue-300 mb-1">Gunakan nomor ini untuk pembayaran SPP</p>
            <p class="text-4xl font-bold font-mono tracking-widest">{{ $siswa->virtualAccount->nomor_va }}</p>
            <p class="text-sm text-blue-300 mt-3">Status: <span class="font-semibold text-white">{{ $siswa->virtualAccount->status }}</span></p>
        </div>
        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
            <strong>Petunjuk Pembayaran:</strong>
            <ol class="list-decimal list-inside mt-2 space-y-1">
                <li>Buka halaman Tagihan Saya dan pilih tagihan yang ingin dibayar.</li>
                <li>Klik tombol <strong>Bayar</strong> untuk membuka simulasi Payment Gateway.</li>
                <li>Konfirmasi pembayaran — sistem akan memproses dan memperbarui status tagihan otomatis.</li>
            </ol>
        </div>
        @else
        <p class="text-gray-400">Nomor Virtual Account belum tersedia. Hubungi administrator.</p>
        @endif
    </div>
</x-app-layout>
