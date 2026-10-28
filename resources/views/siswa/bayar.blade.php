<x-app-layout title="Bayar Tagihan">
    @php
        $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    @endphp

    <div class="max-w-lg space-y-5">
        {{-- Invoice summary --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Detail Tagihan</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-gray-500">Nama</dt><dd class="font-semibold">{{ $siswa->nama }}</dd></div>
                <div><dt class="text-gray-500">NIS</dt><dd class="font-mono">{{ $siswa->nis }}</dd></div>
                <div><dt class="text-gray-500">Bulan/Tahun</dt><dd class="font-semibold">{{ $nb[$tagihan->bulan] }} {{ $tagihan->tahun }}</dd></div>
                <div><dt class="text-gray-500">Tagihan</dt><dd class="font-bold text-xl text-blue-700">Rp {{ number_format($tagihan->nominal,0,',','.') }}</dd></div>
                <div><dt class="text-gray-500">Jatuh Tempo</dt><dd>{{ $tagihan->tanggal_jatuh_tempo->format('d/m/Y') }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd><x-status-badge :status="$tagihan->status" /></dd></div>
                <div class="col-span-2">
                    <dt class="text-gray-500">No. Virtual Account (Sekolah)</dt>
                    <dd class="font-mono font-bold text-2xl text-blue-700">{{ $siswa->virtualAccount->nomor_va ?? 'Belum ada' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Midtrans option --}}
        @if($midtransEnabled)
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-1">Bayar via Midtrans (Real Gateway)</h2>
            <p class="text-xs text-gray-400 mb-4">Sandbox — tidak ada uang sungguhan yang diproses</p>
            <form method="POST" action="{{ route('siswa.tagihan.generate-midtrans', $tagihan->id_tagihan) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Bank</label>
                    <select name="bank" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        @foreach($banks as $b)
                            <option value="{{ $b }}">{{ strtoupper($b) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700">
                    🏦 Bayar via Midtrans →
                </button>
            </form>
        </div>
        @endif

        {{-- Simulator option --}}
        @if($simulatorEnabled)
        <div class="bg-white rounded-xl shadow p-6 border-2 border-dashed border-yellow-400">
            <div class="flex items-center gap-2 mb-2">
                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded">SIMULASI</span>
                <h2 class="font-semibold text-gray-700">Bayar via Simulator</h2>
            </div>
            <p class="text-xs text-gray-400 mb-4">Untuk demonstrasi dan skenario tanggal khusus (§11 PRD). Tidak tersedia di production.</p>
            <a href="{{ route('siswa.tagihan.generate-simulator', $tagihan->id_tagihan) }}"
               class="block w-full py-3 bg-yellow-500 text-white rounded-xl font-semibold text-center hover:bg-yellow-600">
                🧪 Simulasikan Pembayaran →
            </a>
        </div>
        @endif

        @if(!$midtransEnabled && !$simulatorEnabled)
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-red-700 text-sm">
            Tidak ada provider pembayaran yang aktif. Hubungi administrator untuk konfigurasi Midtrans.
        </div>
        @endif
    </div>
</x-app-layout>
