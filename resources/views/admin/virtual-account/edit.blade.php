<x-app-layout title="Edit Virtual Account">
    <div class="max-w-lg bg-white rounded-xl shadow p-6">
        <div class="mb-4 bg-gray-50 rounded-lg p-4 text-sm">
            <p><span class="text-gray-500">Siswa:</span> <strong>{{ $virtualAccount->siswa->nama }}</strong> ({{ $virtualAccount->siswa->nis }})</p>
            <p><span class="text-gray-500">No. VA:</span> <strong class="font-mono text-blue-700">{{ $virtualAccount->nomor_va }}</strong></p>
        </div>
        <form method="POST" action="{{ route('admin.virtual-account.update', $virtualAccount) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="aktif" {{ $virtualAccount->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $virtualAccount->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                <a href="{{ route('admin.virtual-account.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
