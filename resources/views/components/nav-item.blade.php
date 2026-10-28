@props(['route', 'icon' => ''])
@php
    $active = request()->routeIs($route) || (str_ends_with($route, '.index') && request()->routeIs(str_replace('.index', '.*', $route)));
@endphp
<a href="{{ route($route) }}"
   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs sm:text-sm transition-all duration-150
          {{ $active 
             ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-sm shadow-emerald-600/25 font-semibold' 
             : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
    <span class="text-base leading-none transition-transform group-hover:scale-110">{{ $icon }}</span>
    <span class="truncate">{{ $slot }}</span>
</a>
