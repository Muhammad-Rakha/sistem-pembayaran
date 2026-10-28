<x-app-layout title="Manajemen Kelas">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari kelas..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 outline-none focus:ring-2 focus:ring-blue-300">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
            </form>
            <a href="{{ route('admin.kelas.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">+ Tambah</a>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">#</th><th class="pb-2 pr-4">Nama Kelas</th><th class="pb-2 pr-4">Tingkat</th>
                <th class="pb-2 pr-4">Jurusan</th><th class="pb-2 pr-4">Tahun Ajaran</th><th class="pb-2">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($kelas as $i => $k)
                <tr>
                    <td class="py-2 pr-4 text-gray-400">{{ $kelas->firstItem() + $i }}</td>
                    <td class="py-2 pr-4 font-medium">{{ $k->nama_kelas }}</td>
                    <td class="py-2 pr-4">{{ $k->tingkat }}</td>
                    <td class="py-2 pr-4">{{ $k->jurusan->nama_jurusan ?? '-' }}</td>
                    <td class="py-2 pr-4">{{ $k->tahunAjaran->tahun_ajaran ?? '-' }}</td>
                    <td class="py-2">
                        <x-table-actions :editRoute="route('admin.kelas.edit', $k)" :deleteRoute="route('admin.kelas.destroy', $k)" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <x-pagination :paginator="$kelas" />
    </div>
</x-app-layout>
