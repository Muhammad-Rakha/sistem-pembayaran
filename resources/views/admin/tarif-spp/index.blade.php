<x-app-layout title="Tarif SPP">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-end mb-4">
            <a href="{{ route('admin.tarif-spp.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">+ Tambah</a>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">#</th><th class="pb-2 pr-4">Tahun Ajaran</th><th class="pb-2 pr-4">Nominal</th><th class="pb-2 pr-4">Keterangan</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($tarif as $i => $t)
                <tr>
                    <td class="py-2 pr-4 text-gray-400">{{ $tarif->firstItem() + $i }}</td>
                    <td class="py-2 pr-4">{{ $t->tahunAjaran->tahun_ajaran ?? '-' }}</td>
                    <td class="py-2 pr-4 font-semibold">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                    <td class="py-2 pr-4 text-gray-500">{{ $t->keterangan ?? '-' }}</td>
                    <td class="py-2 pr-4"><x-status-badge :status="$t->status" /></td>
                    <td class="py-2">
                        <x-table-actions :editRoute="route('admin.tarif-spp.edit', $t)" :deleteRoute="route('admin.tarif-spp.destroy', $t)" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <x-pagination :paginator="$tarif" />
    </div>
</x-app-layout>
