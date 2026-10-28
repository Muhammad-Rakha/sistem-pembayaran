@php
    $labelRole = [
        'administrator'  => 'Administrator',
        'kepala_sekolah' => 'Kepala Sekolah',
        'teller'         => 'Teller',
        'siswa'          => 'Siswa',
    ];
    $role = Auth::user()->getRoleNames()->first();
@endphp

{{-- Navigasi sederhana: navigasi bawaan Breeze menautkan ke halaman profil yang sudah kita hapus. --}}
<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="font-semibold text-blue-700">SPP SMK Bina Bangsa</a>

        <div class="flex items-center gap-4 text-sm text-gray-600">
            <span>{{ Auth::user()->nama }} ({{ $labelRole[$role] ?? $role }})</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-red-600 hover:underline">Keluar</button>
            </form>
        </div>
    </div>
</nav>
