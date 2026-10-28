<x-app-layout title="Data Siswa">
    <div class="bg-white rounded-xl shadow p-6">
        <form method="GET" class="flex gap-2 mb-4">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari NIS / NISN / Nama..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72 outline-none focus:ring-2 focus:ring-blue-300">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
        </form>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-2 pr-4">NIS</th><th class="pb-2 pr-4">Nama</th><th class="pb-2 pr-4">Kelas</th>
                <th class="pb-2 pr-4">No. VA</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Detail</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($siswa as $s)
                <tr>
                    <td class="py-2 pr-4 font-mono">{{ $s->nis }}</td>
                    <td class="py-2 pr-4">{{ $s->nama }}</td>
                    <td class="py-2 pr-4">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    <td class="py-2 pr-4 font-mono text-xs">{{ $s->virtualAccount->nomor_va ?? '-' }}</td>
                    <td class="py-2 pr-4"><x-status-badge :status="$s->status" /></td>
                    <td class="py-2"><a href="{{ route('kepsek.siswa.show', $s) }}" class="text-blue-600 text-xs hover:underline">Detail</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <x-pagination :paginator="$siswa" />
    </div>
</x-app-layout>
