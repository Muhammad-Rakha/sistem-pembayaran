<x-app-layout title="Manajemen Siswa">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari NIS / NISN / Nama / VA..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-80 outline-none focus:ring-2 focus:ring-blue-300">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
            </form>
            <a href="{{ route('admin.siswa.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">+ Tambah Siswa</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">#</th><th class="pb-2 pr-4">NIS</th><th class="pb-2 pr-4">Nama</th>
                    <th class="pb-2 pr-4">Kelas</th><th class="pb-2 pr-4">No. VA</th>
                    <th class="pb-2 pr-4">Status</th><th class="pb-2">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($siswa as $i => $s)
                    <tr>
                        <td class="py-2 pr-4 text-gray-400">{{ $siswa->firstItem() + $i }}</td>
                        <td class="py-2 pr-4 font-mono">{{ $s->nis }}</td>
                        <td class="py-2 pr-4 font-medium">{{ $s->nama }}</td>
                        <td class="py-2 pr-4">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                        <td class="py-2 pr-4 font-mono text-xs">{{ $s->virtualAccount->nomor_va ?? '-' }}</td>
                        <td class="py-2 pr-4"><x-status-badge :status="$s->status" /></td>
                        <td class="py-2">
                            <x-table-actions
                                :showRoute="route('admin.siswa.show', $s)"
                                :editRoute="route('admin.siswa.edit', $s)"
                                :deleteRoute="route('admin.siswa.destroy', $s)" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$siswa" />
    </div>
</x-app-layout>
