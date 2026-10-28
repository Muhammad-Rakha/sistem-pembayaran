<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' – ' : '' }}{{ config('app.name', 'SPP SMK Bina Bangsa') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased h-full flex flex-col selection:bg-emerald-600 selection:text-white">

<div class="min-h-screen flex flex-col md:flex-row">

    {{-- Modern Clean Sidebar (White card background with Calm Blue & Jade details) --}}
    <aside class="w-full md:w-64 bg-white border-r border-slate-100 flex flex-col shrink-0">
        
        <!-- School Brand Header -->
        <div class="h-20 flex items-center px-6 border-b border-slate-100/80 gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-bold text-lg shadow-sm shadow-emerald-600/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                </svg>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="font-extrabold text-slate-900 text-sm tracking-tight leading-tight block">
                    SPP Bina Bangsa
                </a>
                <span class="text-[11px] font-medium text-emerald-600">Portal Pembayaran</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">

            @auth
                @php $role = auth()->user()->getRoleNames()->first(); @endphp

                {{-- ADMINISTRATOR --}}
                @if($role === 'administrator')
                    <x-nav-item route="admin.dashboard" icon="🏠">Dashboard</x-nav-item>
                    
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Master Data</div>
                    <x-nav-item route="admin.users.index" icon="👥">Pengguna</x-nav-item>
                    <x-nav-item route="admin.siswa.index" icon="🎓">Siswa</x-nav-item>
                    <x-nav-item route="admin.jurusan.index" icon="📚">Jurusan</x-nav-item>
                    <x-nav-item route="admin.kelas.index" icon="🏫">Kelas</x-nav-item>
                    <x-nav-item route="admin.tahun-ajaran.index" icon="📅">Tahun Ajaran</x-nav-item>
                    <x-nav-item route="admin.tarif-spp.index" icon="💰">Tarif SPP</x-nav-item>
                    <x-nav-item route="admin.virtual-account.index" icon="💳">Virtual Account</x-nav-item>
                    
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Keuangan</div>
                    <x-nav-item route="admin.tagihan.index" icon="📋">Tagihan SPP</x-nav-item>
                    <x-nav-item route="admin.transaksi.index" icon="🔄">Transaksi</x-nav-item>
                    <x-nav-item route="admin.pembayaran.index" icon="✅">Pembayaran</x-nav-item>
                    
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Laporan</div>
                    <x-nav-item route="admin.laporan.pembayaran" icon="📊">Laporan Pembayaran</x-nav-item>
                    <x-nav-item route="admin.laporan.tunggakan" icon="⚠️">Laporan Tunggakan</x-nav-item>
                    <x-nav-item route="admin.laporan.rekap" icon="📑">Rekap Pembayaran</x-nav-item>
                    
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sistem</div>
                    <x-nav-item route="admin.notifikasi.index" icon="🔔">Log Notifikasi</x-nav-item>
                    @if(config('midtrans.simulator_enabled') && !config('midtrans.is_production'))
                    <x-nav-item route="admin.simulator.console" icon="🧪">Simulator Console</x-nav-item>
                    @endif
                @endif

                {{-- KEPALA SEKOLAH --}}
                @if($role === 'kepala_sekolah')
                    <x-nav-item route="kepala-sekolah.dashboard" icon="🏠">Dashboard</x-nav-item>
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Data</div>
                    <x-nav-item route="kepsek.siswa.index" icon="🎓">Siswa</x-nav-item>
                    <x-nav-item route="kepsek.tagihan.index" icon="📋">Tagihan SPP</x-nav-item>
                    <x-nav-item route="kepsek.transaksi.index" icon="🔄">Transaksi</x-nav-item>
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Laporan</div>
                    <x-nav-item route="kepsek.laporan.pembayaran" icon="📊">Laporan Pembayaran</x-nav-item>
                    <x-nav-item route="kepsek.laporan.tunggakan" icon="⚠️">Laporan Tunggakan</x-nav-item>
                @endif

                {{-- TELLER --}}
                @if($role === 'teller')
                    <x-nav-item route="teller.dashboard" icon="🏠">Dashboard</x-nav-item>
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pembayaran</div>
                    <x-nav-item route="teller.cari" icon="🔍">Cari Siswa / VA</x-nav-item>
                    <x-nav-item route="teller.transaksi.index" icon="🔄">Transaksi</x-nav-item>
                    <x-nav-item route="teller.tagihan.index" icon="📋">Tagihan SPP</x-nav-item>
                    <div class="pt-4 pb-1.5 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Laporan</div>
                    <x-nav-item route="teller.laporan.pembayaran" icon="📊">Laporan Pembayaran</x-nav-item>
                    <x-nav-item route="teller.laporan.tunggakan" icon="⚠️">Laporan Tunggakan</x-nav-item>
                @endif

                {{-- SISWA --}}
                @if($role === 'siswa')
                    <x-nav-item route="siswa.dashboard" icon="🏠">Dashboard</x-nav-item>
                    <x-nav-item route="siswa.tagihan" icon="📋">Tagihan Saya</x-nav-item>
                    <x-nav-item route="siswa.riwayat" icon="📜">Riwayat Pembayaran</x-nav-item>
                    <x-nav-item route="siswa.virtual-account" icon="💳">Virtual Account</x-nav-item>
                    <x-nav-item route="siswa.profile" icon="👤">Profil Saya</x-nav-item>
                @endif
            @endauth
        </nav>

        {{-- User info + logout footer --}}
        @auth
        <div class="p-4 border-t border-slate-100 bg-slate-50/60">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-sky-500 to-emerald-500 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'U', 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="font-semibold text-xs text-slate-800 truncate">{{ auth()->user()->nama }}</p>
                        <p class="text-[11px] text-emerald-600 font-medium capitalize">{{ str_replace('_', ' ', auth()->user()->getRoleNames()->first()) }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-rose-600 transition duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-w-0 bg-[#F8FAFC]">
        
        {{-- Top Bar with Calm Blue & Jade Accents --}}
        <header class="bg-white border-b border-slate-100 h-20 flex items-center justify-between px-6 sm:px-8 shrink-0">
            <div class="flex items-center gap-3">
                <h1 class="text-slate-800 font-bold text-base sm:text-lg tracking-tight">
                    {{ $title ?? config('app.name') }}
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <!-- Academic Year Badge -->
                <div class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-sky-50/80 border border-sky-100 text-xs font-semibold text-sky-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Tahun Ajaran Aktif</span>
                </div>

                <!-- Midtrans Sandbox indicator if applicable -->
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50/80 border border-emerald-100 text-xs font-semibold text-emerald-700">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Gateway Online</span>
                </div>
            </div>
        </header>

        <main class="flex-1 p-6 sm:p-8 overflow-auto">
            {{-- Flash messages with rounded card styling --}}
            @if(session('success'))
                <div class="mb-6 px-4 py-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 px-4 py-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs sm:text-sm flex items-center gap-2.5 shadow-xs">
                    <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs">✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 px-4 py-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs sm:text-sm shadow-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

</body>
</html>
