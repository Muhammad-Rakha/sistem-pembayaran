@props(['editRoute' => null, 'deleteRoute' => null, 'showRoute' => null, 'deleteConfirm' => 'Yakin ingin menghapus data ini?'])
<div class="flex items-center gap-2">
    @if($showRoute)
        <a href="{{ $showRoute }}" class="text-blue-600 hover:underline text-xs">Detail</a>
    @endif
    @if($editRoute)
        <a href="{{ $editRoute }}" class="text-indigo-600 hover:underline text-xs">Edit</a>
    @endif
    @if($deleteRoute)
        <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('{{ $deleteConfirm }}')">
            @csrf @method('DELETE')
            <button class="text-red-500 hover:underline text-xs">Hapus</button>
        </form>
    @endif
</div>
