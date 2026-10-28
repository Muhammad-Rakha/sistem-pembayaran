<x-app-layout title="Dashboard Siswa">

    {{-- Welcome card --}}
    <div class="bg-blue-700 text-white rounded-xl p-6 mb-6">
        <h2 class="text-xl font-bold">Selamat datang, {{ $siswa->nama }}</h2>
        <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-blue-300">NIS</p>
                <p class="font-semibold">{{ $siswa->nis }}</p>
            </div>
            <div>
                <p class="text-blue-300">Kelas</p>
                <p class="font-semibold">{{ $siswa->kelas->nama_kelas ?? '-' }}</p>
            </div>
            <div>
                <p class="text-blue-300">Jurusan</p>
                <p class="font-semibold">{{ $siswa->kelas->jurusan->nama_jurusan ?? '-' }}</p>
            </div>
            <div>
                <p class="text-blue-300">No. Virtual Account</p>
                <p class="font-semibold font-mono">{{ $siswa->virtualAccount->nomor_va ?? 'Belum ada' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Tagihan bulan ini --}}
        <div class="bg-white rounded-xl shadow p-5 lg:col-span-2">
            <h3 class="font-semibold text-gray-700 mb-3">Tagihan Bulan Ini</h3>
            @if($tagihanBulanIni)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-blue-700">Rp {{ number_format($tagihanBulanIni->nominal,0,',','.') }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Jatuh tempo: {{ $tagihanBulanIni->tanggal_jatuh_tempo->translatedFormat('d F Y') ?? $tagihanBulanIni->tanggal_jatuh_tempo->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <x-status-badge :status="$tagihanBulanIni->status" />
                        @if($tagihanBulanIni->status !== 'PAID')
                            <a href="{{ route('siswa.tagihan.bayar', $tagihanBulanIni->id_tagihan) }}"
                               class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                                Bayar Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-gray-400">Tidak ada tagihan bulan ini.</p>
            @endif
        </div>

        {{-- Tunggakan --}}
        <div class="bg-white rounded-xl shadow p-5">
            <h3 class="font-semibold text-gray-700 mb-3">Tunggakan</h3>
            <p class="text-3xl font-bold text-red-600">{{ $jumlahTunggakan }}</p>
            <p class="text-sm text-gray-500">tagihan overdue</p>
            <p class="text-lg font-semibold text-red-500 mt-2">Rp {{ number_format($totalTunggakan,0,',','.') }}</p>
            <p class="text-sm text-gray-400">total tunggakan</p>
        </div>
    </div>

    {{-- Payment history --}}
    <div class="bg-white rounded-xl shadow p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-700">Riwayat Pembayaran Terakhir</h3>
            <a href="{{ route('siswa.riwayat') }}" class="text-blue-600 text-sm hover:underline">Lihat semua →</a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">Bulan</th>
                    <th class="pb-2 pr-4">Nominal</th>
                    <th class="pb-2 pr-4">Status</th>
                    <th class="pb-2">Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($riwayatTerakhir as $t)
                @php
                    $namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                @endphp
                <tr>
                    <td class="py-2 pr-4">{{ $namaBulan[$t->bulan] ?? $t->bulan }} {{ $t->tahun }}</td>
                    <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                    <td class="py-2 pr-4"><x-status-badge :status="$t->status" /></td>
                    <td class="py-2 text-gray-500">{{ $t->pembayaran ? $t->pembayaran->tanggal_pembayaran->format('d/m/Y') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-400">Belum ada riwayat pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
