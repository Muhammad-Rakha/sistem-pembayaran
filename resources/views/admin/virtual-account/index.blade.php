<x-app-layout title="Virtual Account">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari No. VA / NIS / Nama..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72 outline-none focus:ring-2 focus:ring-blue-300">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
            </form>
            <a href="{{ route('admin.virtual-account.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">+ Tambah VA</a>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">#</th><th class="pb-2 pr-4">No. Virtual Account</th><th class="pb-2 pr-4">NIS</th>
                <th class="pb-2 pr-4">Nama Siswa</th><th class="pb-2 pr-4">Kelas</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($va as $i => $v)
                <tr>
                    <td class="py-2 pr-4 text-gray-400">{{ $va->firstItem() + $i }}</td>
                    <td class="py-2 pr-4 font-mono font-bold text-blue-700">{{ $v->nomor_va }}</td>
                    <td class="py-2 pr-4 font-mono">{{ $v->siswa->nis ?? '-' }}</td>
                    <td class="py-2 pr-4">{{ $v->siswa->nama ?? '-' }}</td>
                    <td class="py-2 pr-4">{{ $v->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td class="py-2 pr-4"><x-status-badge :status="$v->status" /></td>
                    <td class="py-2">
                        <x-table-actions
                            :showRoute="route('admin.virtual-account.show', $v)"
                            :editRoute="route('admin.virtual-account.edit', $v)"
                            :deleteRoute="route('admin.virtual-account.destroy', $v)" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <x-pagination :paginator="$va" />
    </div>
</x-app-layout>
