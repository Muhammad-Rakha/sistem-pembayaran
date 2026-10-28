<x-app-layout title="Tahun Ajaran">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-end mb-4">
            <a href="{{ route('admin.tahun-ajaran.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">+ Tambah</a>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">#</th><th class="pb-2 pr-4">Tahun Ajaran</th><th class="pb-2 pr-4">Mulai</th><th class="pb-2 pr-4">Selesai</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($list as $i => $ta)
                <tr>
                    <td class="py-2 pr-4 text-gray-400">{{ $list->firstItem() + $i }}</td>
                    <td class="py-2 pr-4 font-medium">{{ $ta->tahun_ajaran }}</td>
                    <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($ta->tanggal_mulai)->format('d/m/Y') }}</td>
                    <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($ta->tanggal_selesai)->format('d/m/Y') }}</td>
                    <td class="py-2 pr-4"><x-status-badge :status="$ta->status" /></td>
                    <td class="py-2">
                        <x-table-actions :editRoute="route('admin.tahun-ajaran.edit', $ta)" :deleteRoute="route('admin.tahun-ajaran.destroy', $ta)" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <x-pagination :paginator="$list" />
    </div>
</x-app-layout>
