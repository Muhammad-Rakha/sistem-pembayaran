<x-app-layout title="Tagihan Saya">
    @php $nb = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-semibold text-gray-700 mb-4">Daftar Tagihan SPP</h2>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">Bulan/Tahun</th><th class="pb-2 pr-4">Nominal</th>
                <th class="pb-2 pr-4">Jatuh Tempo</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tagihan as $t)
                <tr>
                    <td class="py-2 pr-4 font-semibold">{{ $nb[$t->bulan] }} {{ $t->tahun }}</td>
                    <td class="py-2 pr-4">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                    <td class="py-2 pr-4">{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                    <td class="py-2 pr-4"><x-status-badge :status="$t->status" /></td>
                    <td class="py-2">
                        @if($t->status !== 'PAID')
                            <a href="{{ route('siswa.tagihan.bayar', $t->id_tagihan) }}"
                               class="px-3 py-1 bg-green-600 text-white rounded-lg text-xs hover:bg-green-700">Bayar</a>
                        @else
                            <span class="text-gray-400 text-xs">Lunas</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada tagihan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$tagihan" />
    </div>
</x-app-layout>
