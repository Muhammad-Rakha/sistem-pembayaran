<x-app-layout title="Manajemen Jurusan">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari jurusan..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 outline-none focus:ring-2 focus:ring-blue-300">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
            </form>
            <a href="{{ route('admin.jurusan.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">+ Tambah</a>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">#</th><th class="pb-2 pr-4">Kode</th><th class="pb-2 pr-4">Nama Jurusan</th><th class="pb-2 pr-4">Keterangan</th><th class="pb-2">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($jurusan as $i => $j)
                <tr>
                    <td class="py-2 pr-4 text-gray-400">{{ $jurusan->firstItem() + $i }}</td>
                    <td class="py-2 pr-4 font-mono font-semibold">{{ $j->kode_jurusan }}</td>
                    <td class="py-2 pr-4 font-medium">{{ $j->nama_jurusan }}</td>
                    <td class="py-2 pr-4 text-gray-500">{{ $j->keterangan ?? '-' }}</td>
                    <td class="py-2">
                        <x-table-actions :editRoute="route('admin.jurusan.edit', $j)" :deleteRoute="route('admin.jurusan.destroy', $j)" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <x-pagination :paginator="$jurusan" />
    </div>
</x-app-layout>
