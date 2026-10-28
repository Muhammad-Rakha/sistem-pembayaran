<x-app-layout title="Laporan Tunggakan">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-7">
        
        {{-- Section Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="font-bold text-base sm:text-lg text-slate-800">Laporan Tunggakan SPP</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar tagihan yang telah melewati tanggal jatuh tempo (OVERDUE)</p>
            </div>
            <div>
                <a href="{{ route('admin.laporan.tunggakan.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-rose-600/20 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export Excel</span>
                </a>
            </div>
        </div>

        {{-- Filter Panel --}}
        <form method="GET" class="bg-slate-50/70 border border-slate-200/60 rounded-2xl p-4 sm:p-5 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                
                <!-- Bulan Select -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Bulan Tagihan</label>
                    <select name="bulan" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-700 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                        <option value="">Semua Bulan</option>
                        @php $namaBulanIndo = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                        @foreach(range(1,12) as $b)
                            <option value="{{ $b }}" {{ $filters['bulan'] == $b ? 'selected' : '' }}>{{ $namaBulanIndo[$b] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tahun Input -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun</label>
                    <input type="number" name="tahun" value="{{ $filters['tahun'] }}" placeholder="Contoh: {{ now()->year }}" 
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-700 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                </div>

                <!-- Kelas Select -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kelas</label>
                    <select name="id_kelas" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-700 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}" {{ $filters['id_kelas'] == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cari Siswa</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Nama / NIS..." 
                               class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-xs font-medium text-slate-700 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                    </div>
                </div>

                <!-- Action Button -->
                <div class="sm:col-span-2 lg:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-slate-200/50">
                    @if(request()->hasAny(['bulan', 'tahun', 'id_kelas', 'q']))
                        <a href="{{ url()->current() }}" class="py-2 px-3.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-xl text-xs font-medium transition">
                            Reset
                        </a>
                    @endif
                    <button type="submit" class="py-2 px-5 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-sky-600/20 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        <span>Filter Tunggakan</span>
                    </button>
                </div>

            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @php $nb = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-100">
                        <th class="pb-3 pr-4">NIS</th>
                        <th class="pb-3 pr-4">Nama Siswa</th>
                        <th class="pb-3 pr-4">Kelas</th>
                        <th class="pb-3 pr-4">Bulan Tunggakan</th>
                        <th class="pb-3 pr-4">Nominal</th>
                        <th class="pb-3 text-right">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($tunggakan as $t)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 pr-4 font-mono font-semibold text-slate-900">{{ $t->siswa->nis ?? '-' }}</td>
                        <td class="py-3.5 pr-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px] flex items-center justify-center">
                                    {{ strtoupper(substr($t->siswa->nama ?? 'S', 0, 1)) }}
                                </div>
                                <span class="font-bold text-slate-800">{{ $t->siswa->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 pr-4 text-slate-600">{{ $t->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td class="py-3.5 pr-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                {{ $nb[$t->bulan] }} {{ $t->tahun }}
                            </span>
                        </td>
                        <td class="py-3.5 pr-4 font-bold text-slate-900">Rp {{ number_format($t->nominal,0,',','.') }}</td>
                        <td class="py-3.5 text-right text-rose-600 font-semibold">{{ $t->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Tidak ada data tunggakan untuk filter yang dipilih.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 pt-3 border-t border-slate-100">
            <x-pagination :paginator="$tunggakan" />
        </div>
    </div>
</x-app-layout>
