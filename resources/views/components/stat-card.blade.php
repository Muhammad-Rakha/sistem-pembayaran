@props(['label', 'value', 'color' => 'blue', 'icon' => ''])
@php
    $palettes = [
        'blue'   => [
            'badge' => 'bg-sky-50 text-sky-600 border border-sky-100',
            'border' => 'border-slate-100',
            'accent' => 'text-sky-700',
        ],
        'green'  => [
            'badge' => 'bg-emerald-50 text-emerald-600 border border-emerald-100',
            'border' => 'border-slate-100',
            'accent' => 'text-emerald-700',
        ],
        'red'    => [
            'badge' => 'bg-rose-50 text-rose-600 border border-rose-100',
            'border' => 'border-slate-100',
            'accent' => 'text-rose-700',
        ],
        'yellow' => [
            'badge' => 'bg-amber-50 text-amber-600 border border-amber-100',
            'border' => 'border-slate-100',
            'accent' => 'text-amber-700',
        ],
    ];
    $style = $palettes[$color] ?? $palettes['blue'];
@endphp
<div class="bg-white rounded-2xl border {{ $style['border'] }} p-5 shadow-sm hover:shadow-md transition duration-200">
    <div class="flex items-center justify-between mb-3">
        <span class="text-xs font-medium text-slate-500 tracking-tight">{{ $label }}</span>
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base {{ $style['badge'] }}">
            {{ $icon }}
        </div>
    </div>
    <div class="text-2xl font-bold text-slate-800 tracking-tight">{{ $value }}</div>
</div>
