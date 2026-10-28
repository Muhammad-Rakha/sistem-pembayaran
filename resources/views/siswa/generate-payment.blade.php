<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simulasi Payment Gateway – SPP SMK Bina Bangsa</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-gray-100 min-h-screen flex items-center justify-center p-4">
@php
    $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $simulatorSecret = config('midtrans.simulator_secret', '');
@endphp

<div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        {{-- SIMULATION header --}}
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-4 text-center">
            <span class="bg-white text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">⚠️ SIMULASI — BUKAN TRANSAKSI NYATA</span>
        </div>
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-white text-center">
            <p class="text-xs uppercase tracking-widest text-blue-200 mb-1">Local Payment Gateway Simulator</p>
            <h1 class="text-xl font-bold">Virtual Account Payment</h1>
            <p class="text-blue-200 text-sm mt-1">SMK Bina Bangsa</p>
        </div>

        <div class="p-6">
            <div id="step-confirm">
                <div class="bg-gray-50 rounded-xl p-4 mb-5 text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama Siswa</span>
                        <span class="font-semibold">{{ $siswa->nama }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">NIS</span>
                        <span class="font-mono">{{ $siswa->nis }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tagihan</span>
                        <span class="font-semibold">{{ $nb[$tagihan->bulan] }} {{ $tagihan->tahun }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">No. VA (Sekolah)</span>
                        <span class="font-mono font-bold text-blue-700">{{ $siswa->virtualAccount->nomor_va }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between">
                        <span class="text-gray-500">Total Pembayaran</span>
                        <span class="text-xl font-bold text-blue-700">Rp {{ number_format($tagihan->nominal,0,',','.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>Transaction ID</span>
                        <span class="font-mono">{{ $transactionId }}</span>
                    </div>
                </div>

                {{-- Custom paid_at for date scenario demos --}}
                <div class="mb-4 text-sm">
                    <label class="block text-gray-600 mb-1 font-medium">Waktu Pembayaran <span class="text-xs text-gray-400">(ubah untuk skenario tanggal)</span></label>
                    <input type="text" id="paid_at_input"
                        value="{{ now()->format('Y-m-d H:i:s') }}"
                        class="datetimepicker w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500 bg-white cursor-pointer">
                </div>

                <button id="btn-pay" onclick="simulatePayment()"
                    class="w-full py-3 bg-green-600 text-white rounded-xl font-bold text-lg hover:bg-green-700 transition">
                    ✅ Konfirmasi & Bayar (Simulasi)
                </button>
                <a href="{{ route('siswa.tagihan') }}" class="block text-center text-sm text-gray-400 mt-3 hover:underline">Batal</a>
            </div>

            <div id="step-processing" class="hidden text-center py-6">
                <div class="animate-spin text-5xl mb-4">⏳</div>
                <p class="text-blue-700 font-semibold">Mengirim callback ke webhook...</p>
                <p class="text-xs text-gray-400 mt-1">Harap tunggu sebentar</p>
            </div>

            <div id="step-success" class="hidden text-center py-4">
                <div class="text-6xl mb-3">✅</div>
                <h2 class="text-2xl font-bold text-green-700">Pembayaran Berhasil!</h2>
                <p class="text-sm text-gray-500 mt-2">Webhook diterima dan diproses. Status tagihan diperbarui otomatis.</p>
                <div id="success-detail" class="bg-green-50 rounded-lg p-3 mt-4 text-sm text-left space-y-1"></div>
                <a href="{{ route('siswa.tagihan') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Lihat Tagihan
                </a>
            </div>

            <div id="step-error" class="hidden text-center py-4">
                <div class="text-5xl mb-3">❌</div>
                <h2 class="text-xl font-bold text-red-600">Pembayaran Gagal</h2>
                <p id="error-msg" class="text-sm text-gray-500 mt-2"></p>
                <button onclick="location.reload()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Coba Lagi</button>
            </div>
        </div>
    </div>
    <p class="text-center text-xs text-gray-400 mt-3">⚠️ Ini adalah simulasi untuk demonstrasi LSP. Tidak ada uang yang diproses.</p>
</div>

<script>
async function simulatePayment() {
    const paidAt = document.getElementById('paid_at_input').value.replace('T', ' ') + ':00';
    document.getElementById('step-confirm').classList.add('hidden');
    document.getElementById('step-processing').classList.remove('hidden');

    await new Promise(r => setTimeout(r, 1200));

    const payload = {
        virtual_account: "{{ $siswa->virtualAccount->nomor_va }}",
        amount: {{ $tagihan->nominal }},
        transaction_id: "{{ $transactionId }}",
        status: "PAID",
        paid_at: paidAt
    };

    const body = JSON.stringify(payload);

    // Compute HMAC-SHA256 server-side by calling a helper endpoint
    // We pass the payload to the server, which signs and forwards to the webhook
    try {
        // Direct call to a signed simulator endpoint (signing done server-side)
        const res = await fetch("{{ route('siswa.tagihan.generate-simulator-send', $tagihan->id_tagihan) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: body
        });

        const data = await res.json();
        document.getElementById('step-processing').classList.add('hidden');

        if (data.success) {
            document.getElementById('step-success').classList.remove('hidden');
            document.getElementById('success-detail').innerHTML = `
                <p><strong>VA:</strong> ${payload.virtual_account}</p>
                <p><strong>Nominal:</strong> Rp {{ number_format($tagihan->nominal,0,',','.') }}</p>
                <p><strong>Transaction ID:</strong> ${payload.transaction_id}</p>
                <p><strong>Waktu Bayar:</strong> ${paidAt}</p>
            `;
        } else {
            document.getElementById('step-error').classList.remove('hidden');
            document.getElementById('error-msg').textContent = data.message || 'Terjadi kesalahan pada webhook.';
        }
    } catch (e) {
        document.getElementById('step-processing').classList.add('hidden');
        document.getElementById('step-error').classList.remove('hidden');
        document.getElementById('error-msg').textContent = 'Koneksi gagal: ' + e.message;
    }
}
</script>
</body>
</html>
