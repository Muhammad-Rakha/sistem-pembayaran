<x-app-layout title="Laporan Tunggakan">
    <div class="bg-white rounded-xl shadow p-6">
        <form method="GET" class="flex flex-wrap gap-2 mb-4">
            <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Bulan</option>
                @foreach(range(1,12) as $b)
                    <option value="{{ $b }}" {{ $filters['bulan'] == $b ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($b)->format('F') }}</option>
                @endforeach
            </select>
            <input type="number" name="tahun" value="{{ $filters['tahun'] }}" placeholder="Tahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 outline-none">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Filter</button>
            <a href="{{ route('teller.laporan.tunggakan.export', request()->query()) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Export Excel</a>
        </form>
        @php $nb = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">NIS</th><th class="pb-2 pr-4">Nama</th><th class="pb-2 pr-4">Kelas</th>
                <th class="pb-2 pr-4">Bulan Tunggakan</th><th class="pb-2 pr-4">Nominal</th><th class="pb-2">Jatuh Tempo</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tunggakan as $t)
                <tr>
                    <td class="py-2 pr-4 font-mono">{{ $t->siswa->nis ?? '-' }}</td>
                    <td class="py-2 pr-4">{{ $t->siswa->nama ?? '-' }}</td>
                    <td class="py-2 pr-4">{{ $t->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="py-2 pr-4 text-red-600 font-semibold">{{ $nb[$t->bulan] }} {{ $t->tahun }}</td>
                    <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                    <td class="py-2">{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-6 text-center text-gray-400">Tidak ada tunggakan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$tunggakan" />
    </div>
</x-app-layout>
