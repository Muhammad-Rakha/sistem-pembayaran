<x-app-layout title="Rekap Pembayaran SPP">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-7">
        
        {{-- Section Header --}}
        <div class="mb-6">
            <h2 class="font-bold text-base sm:text-lg text-slate-800">Rekap Pembayaran SPP</h2>
            <p class="text-xs text-slate-400 mt-0.5">Ringkasan status pembayaran SPP tahunan per bulan</p>
        </div>

        {{-- Filter Panel --}}
        <form method="GET" class="bg-slate-50/70 border border-slate-200/60 rounded-2xl p-4 sm:p-5 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 items-end">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" placeholder="Tahun"
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-700 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kelas</label>
                    <select name="id_kelas" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-700 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition outline-none">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}" {{ $idKelas == $k->id_kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button class="w-full py-2 px-4 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-sky-600/20 transition flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Tampilkan Rekap</span>
                    </button>
                </div>
            </div>
        </form>

        <h3 class="font-bold text-sm text-slate-800 mb-4 flex items-center gap-2">
            <span>Ringkasan SPP Tahun {{ $tahun }}</span>
        </h3>

        {{-- Summary cards --}}
        @php
            $totalLunas    = array_sum(array_column($rekap, 'lunas'));
            $totalBelum    = array_sum(array_column($rekap, 'belum'));
            $totalTunggakan = array_sum(array_column($rekap, 'tunggakan'));
            $totalNominal  = array_sum(array_column($rekap, 'nominal'));
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <x-stat-card label="Total LUNAS" :value="$totalLunas" color="green" icon="✅" />
            <x-stat-card label="BELUM BAYAR" :value="$totalBelum" color="blue" icon="⏳" />
            <x-stat-card label="TUNGGAKAN" :value="$totalTunggakan" color="red" icon="⚠️" />
            <x-stat-card label="Total Diterima" :value="'Rp '.number_format($totalNominal,0,',','.')" color="green" icon="💰" />
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-100">
                        <th class="pb-3 pr-4">Bulan</th>
                        <th class="pb-3 pr-4 text-center">Total Tagihan</th>
                        <th class="pb-3 pr-4 text-center text-emerald-700">LUNAS</th>
                        <th class="pb-3 pr-4 text-center text-sky-700">BELUM BAYAR</th>
                        <th class="pb-3 pr-4 text-center text-rose-600">TUNGGAKAN</th>
                        <th class="pb-3 text-right">Nominal Terkumpul</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @foreach($rekap as $b => $r)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 pr-4 font-bold text-slate-800">{{ $r['bulan'] }}</td>
                        <td class="py-3.5 pr-4 text-center font-semibold text-slate-600">{{ $r['total'] }}</td>
                        <td class="py-3.5 pr-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">{{ $r['lunas'] }}</span>
                        </td>
                        <td class="py-3.5 pr-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-sky-700">{{ $r['belum'] }}</span>
                        </td>
                        <td class="py-3.5 pr-4 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700">{{ $r['tunggakan'] }}</span>
                        </td>
                        <td class="py-3.5 text-right font-extrabold text-slate-900">
                            Rp {{ number_format($r['nominal'],0,',','.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
