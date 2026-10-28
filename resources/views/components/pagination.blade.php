@props(['paginator'])
@if($paginator->hasPages())
<div class="flex items-center justify-between text-sm text-gray-500 mt-4">
    <span>Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data</span>
    <div>{{ $paginator->links() }}</div>
</div>
@endif
