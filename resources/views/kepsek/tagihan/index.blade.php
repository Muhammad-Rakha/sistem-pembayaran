<x-app-layout title="Tagihan SPP">
    <div class="bg-white rounded-xl shadow p-6">
        <form method="GET" class="flex flex-wrap gap-2 mb-4">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari NIS / Nama..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48 outline-none">
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="UNPAID" {{ $status === 'UNPAID' ? 'selected' : '' }}>UNPAID</option>
                <option value="PAID" {{ $status === 'PAID' ? 'selected' : '' }}>PAID</option>
                <option value="OVERDUE" {{ $status === 'OVERDUE' ? 'selected' : '' }}>OVERDUE</option>
            </select>
            <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Bulan</option>
                @foreach(range(1,12) as $b)
                    <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($b)->format('F') }}</option>
                @endforeach
            </select>
            <input type="number" name="tahun" value="{{ $tahun }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 outline-none">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Filter</button>
        </form>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">NIS</th><th class="pb-2 pr-4">Nama</th><th class="pb-2 pr-4">Kelas</th>
                <th class="pb-2 pr-4">Bulan/Thn</th><th class="pb-2 pr-4">Nominal</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Jatuh Tempo</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($tagihan as $t)
                <tr>
                    <td class="py-2 pr-4 font-mono">{{ $t->siswa->nis ?? '-' }}</td>
                    <td class="py-2 pr-4">{{ $t->siswa->nama ?? '-' }}</td>
                    <td class="py-2 pr-4">{{ $t->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="py-2 pr-4">{{ \Carbon\Carbon::create()->month($t->bulan)->format('M') }} {{ $t->tahun }}</td>
                    <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                    <td class="py-2 pr-4"><x-status-badge :status="$t->status" /></td>
                    <td class="py-2">{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <x-pagination :paginator="$tagihan" />
    </div>
</x-app-layout>
