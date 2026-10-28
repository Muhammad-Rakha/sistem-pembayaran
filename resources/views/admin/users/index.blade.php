<x-app-layout title="Manajemen Pengguna">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <form method="GET" class="flex gap-2">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / username..."
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:ring-2 focus:ring-blue-300 outline-none">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cari</button>
            </form>
            <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">+ Tambah Pengguna</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-gray-500 border-b">
                    <th class="pb-2 pr-4">#</th><th class="pb-2 pr-4">Nama</th><th class="pb-2 pr-4">Username</th>
                    <th class="pb-2 pr-4">Role</th><th class="pb-2 pr-4">Status</th><th class="pb-2">Aksi</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $i => $user)
                    <tr>
                        <td class="py-2 pr-4 text-gray-400">{{ $users->firstItem() + $i }}</td>
                        <td class="py-2 pr-4 font-medium">{{ $user->nama }}</td>
                        <td class="py-2 pr-4 font-mono text-xs">{{ $user->username }}</td>
                        <td class="py-2 pr-4">{{ $user->getRoleNames()->first() ?? '-' }}</td>
                        <td class="py-2 pr-4"><x-status-badge :status="$user->status" /></td>
                        <td class="py-2">
                            <x-table-actions
                                :editRoute="route('admin.users.edit', $user)"
                                :deleteRoute="route('admin.users.destroy', $user)" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$users" />
    </div>
</x-app-layout>
