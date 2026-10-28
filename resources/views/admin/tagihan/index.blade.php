<x-app-layout title="Tagihan SPP">
    {{-- Generate form --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-4">
        <h3 class="font-semibold text-blue-800 mb-3">Generate Tagihan Bulanan</h3>
        <form method="POST" action="{{ route('admin.tagihan.generate') }}" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs text-blue-700 mb-1">Tarif SPP</label>
                <select name="id_tarif" required class="border border-blue-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih Tarif --</option>
                    @foreach($tarifAktif as $t)
                        <option value="{{ $t->id_tarif }}">{{ $t->tahunAjaran->tahun_ajaran ?? '' }} — Rp {{ number_format($t->nominal,0,',','.') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-blue-700 mb-1">Bulan</label>
                <select name="bulan" required class="border border-blue-300 rounded-lg px-3 py-2 text-sm">
                    @foreach(range(1,12) as $b)
                        <option value="{{ $b }}" {{ now()->month == $b ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($b)->format('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-blue-700 mb-1">Tahun</label>
                <input type="number" name="tahun" value="{{ now()->year }}" min="2000" max="2100" required
                    class="border border-blue-300 rounded-lg px-3 py-2 text-sm w-24">
            </div>
            <button class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Generate</button>
        </form>
    </div>

    {{-- Overdue trigger (for demo §11, BR-07) --}}
    <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6">
        <h3 class="font-semibold text-red-800 mb-2">Tandai TUNGGAKAN (Demo §11)</h3>
        <p class="text-xs text-red-600 mb-3">Ubah tagihan BELUM BAYAR yang sudah lewat jatuh tempo menjadi TUNGGAKAN. Gunakan tanggal referensi untuk simulasi skenario.</p>
        <form method="POST" action="{{ route('admin.tagihan.trigger-overdue') }}" class="flex gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs text-red-700 mb-1">Tanggal Referensi</label>
                <input type="text" name="reference_date" value="{{ now()->toDateString() }}"
                    class="datepicker border border-red-300 rounded-lg px-3 py-2 text-sm bg-white cursor-pointer">
            </div>
            <button class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700"
                onclick="return confirm('Tandai semua tagihan BELUM BAYAR yang lewat tanggal referensi menjadi TUNGGAKAN?')">
                Tandai TUNGGAKAN
            </button>
        </form>
    </div>

    {{-- Filter + list --}}
    <div class="bg-white rounded-xl shadow p-6">
        <form method="GET" class="flex flex-wrap gap-2 mb-4">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari NIS / Nama..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48 outline-none focus:ring-2 focus:ring-blue-300">
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
            <input type="number" name="tahun" value="{{ $tahun }}" placeholder="Tahun"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 outline-none">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">NIS</th><th class="pb-2 pr-4">Nama Siswa</th><th class="pb-2 pr-4">Kelas</th>
                    <th class="pb-2 pr-4">Bulan/Tahun</th><th class="pb-2 pr-4">Nominal</th>
                    <th class="pb-2 pr-4">Jatuh Tempo</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Detail</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($tagihan as $t)
                    <tr>
                        <td class="py-2 pr-4 font-mono">{{ $t->siswa->nis ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ $t->siswa->nama ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ $t->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td class="py-2 pr-4">{{ \Carbon\Carbon::create()->month($t->bulan)->format('M') }} {{ $t->tahun }}</td>
                        <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="py-2 pr-4">{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                        <td class="py-2 pr-4"><x-status-badge :status="$t->status" /></td>
                        <td class="py-2"><a href="{{ route('admin.tagihan.show', $t) }}" class="text-blue-600 text-xs hover:underline">Detail</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$tagihan" />
    </div>
</x-app-layout>
