@props(['status'])
@php
    // Modern Clean Terminology (Source §19, FR-35) with Jade & Calm Blue & Coral Accents
    $map = [
        // Invoice statuses
        'UNPAID'    => ['label' => 'BELUM BAYAR', 'cls' => 'bg-amber-50 text-amber-700 border border-amber-200/70'],
        'PAID'      => ['label' => 'LUNAS',       'cls' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/70'],
        'OVERDUE'   => ['label' => 'TUNGGAKAN',   'cls' => 'bg-rose-50 text-rose-700 border border-rose-200/70'],
        // Transaction / attempt statuses
        'PENDING'   => ['label' => 'PENDING',     'cls' => 'bg-sky-50 text-sky-700 border border-sky-200/70'],
        'EXPIRED'   => ['label' => 'EXPIRED',     'cls' => 'bg-slate-100 text-slate-600 border border-slate-200'],
        'CANCELLED' => ['label' => 'DIBATALKAN',  'cls' => 'bg-slate-100 text-slate-600 border border-slate-200'],
        'FAILED'    => ['label' => 'GAGAL',       'cls' => 'bg-rose-50 text-rose-600 border border-rose-200'],
        'DENIED'    => ['label' => 'DITOLAK',     'cls' => 'bg-rose-50 text-rose-600 border border-rose-200'],
        // Account statuses
        'aktif'     => ['label' => 'Aktif',       'cls' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/70'],
        'nonaktif'  => ['label' => 'Nonaktif',    'cls' => 'bg-slate-100 text-slate-500 border border-slate-200'],
        'waiting'   => ['label' => 'Menunggu',    'cls' => 'bg-amber-50 text-amber-700 border border-amber-200'],
        'paid'      => ['label' => 'LUNAS',       'cls' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/70'],
        'expired'   => ['label' => 'Kadaluarsa',  'cls' => 'bg-slate-100 text-slate-500 border border-slate-200'],
        'cancelled' => ['label' => 'Dibatalkan',  'cls' => 'bg-slate-100 text-slate-500 border border-slate-200'],
        'failed'    => ['label' => 'Gagal',       'cls' => 'bg-rose-50 text-rose-600 border border-rose-200'],
    ];
    $entry = $map[$status] ?? ['label' => $status, 'cls' => 'bg-slate-100 text-slate-700 border border-slate-200'];
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $entry['cls'] }}">
    {{ $entry['label'] }}
</span>
