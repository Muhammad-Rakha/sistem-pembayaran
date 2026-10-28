<x-app-layout title="Dashboard Administrator">

    <!-- Greeting Banner with Jade & Calm Blue accents -->
    <div class="mb-6 p-6 rounded-3xl bg-gradient-to-r from-emerald-600 via-teal-600 to-sky-700 text-white shadow-lg shadow-emerald-700/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-white text-xs font-semibold mb-2">
                <span>SMK Bina Bangsa</span>
                <span>•</span>
                <span>Tahun Ajaran {{ now()->year }}/{{ now()->year + 1 }}</span>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight">
                Selamat Datang, {{ auth()->user()->nama ?? 'Administrator' }}
            </h2>
            <p class="text-white/80 text-xs sm:text-sm mt-1">
                Pantau transaksi SPP, data tagihan jatuh tempo, dan rekonsiliasi gateway secara real-time.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tagihan.index') }}" class="px-4 py-2.5 rounded-xl bg-white text-emerald-800 text-xs font-bold hover:bg-slate-50 transition shadow-sm">
                Lihat Tagihan
            </a>
            <a href="{{ route('admin.laporan.pembayaran') }}" class="px-4 py-2.5 rounded-xl bg-emerald-800/60 hover:bg-emerald-800 text-white text-xs font-bold transition border border-white/20">
                Laporan Keuangan
            </a>
        </div>
    </div>

    {{-- Stats Row (6 Cards with Soft Rounded styling) --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <x-stat-card label="Siswa Aktif" :value="$totalSiswa" color="blue" icon="🎓" />
        <x-stat-card label="Total Tagihan" :value="$totalTagihan" color="blue" icon="📋" />
        <x-stat-card label="Total Pembayaran" :value="$totalPembayaran" color="green" icon="✅" />
        <x-stat-card label="Transaksi Gateway" :value="$totalTransaksi" color="blue" icon="🔄" />
        <x-stat-card label="Tagihan Overdue" :value="$totalTunggakan" color="red" icon="⚠️" />
        <x-stat-card label="Total Tunggakan" :value="'Rp '.number_format($nominalTunggakan,0,',','.')" color="red" icon="💸" />
    </div>

    {{-- Charts (Clean Card Containers) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h2 class="font-bold text-sm sm:text-base text-slate-800">Pembayaran per Bulan ({{ now()->year }})</h2>
                </div>
                <span class="text-xs text-slate-400 font-medium">Realisasi Masuk (Rp)</span>
            </div>
            <div class="relative h-64">
                <canvas id="chartPembayaran"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <h2 class="font-bold text-sm sm:text-base text-slate-800">Tagihan Overdue per Bulan</h2>
                </div>
                <span class="text-xs text-slate-400 font-medium">Jumlah Siswa Menunggak</span>
            </div>
            <div class="relative h-64">
                <canvas id="chartTunggakan"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Transactions (Modern Clean Table Layout) --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="font-bold text-base text-slate-800">Transaksi Terbaru</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi virtual account dan webhook gateway terkini</p>
            </div>
            <a href="{{ route('admin.transaksi.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                <span>Lihat Semua Transaksi</span>
                <span>→</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left whitespace-nowrap">
                <thead>
                    <tr class="text-slate-400 font-semibold border-b border-slate-100">
                        <th class="pb-3 pr-4">Transaction ID</th>
                        <th class="pb-3 pr-4">Virtual Account</th>
                        <th class="pb-3 pr-4">Siswa</th>
                        <th class="pb-3 pr-4">Nominal</th>
                        <th class="pb-3 pr-4">Status</th>
                        <th class="pb-3 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($recentTransaksi as $trx)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 pr-4 font-mono font-semibold text-slate-900">{{ $trx->transaction_id }}</td>
                        <td class="py-3.5 pr-4 font-mono text-slate-600">{{ $trx->virtual_account }}</td>
                        <td class="py-3.5 pr-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] flex items-center justify-center">
                                    {{ strtoupper(substr($trx->siswa->nama ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-slate-800 font-semibold">{{ $trx->siswa->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 pr-4 font-bold text-slate-900">Rp {{ number_format($trx->nominal,0,',','.') }}</td>
                        <td class="py-3.5 pr-4"><x-status-badge :status="$trx->status_transaksi" /></td>
                        <td class="py-3.5 text-right text-slate-400">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada transaksi pembayaran yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart Configuration matching modern jade & calm blue palette -->
    <script>
    const labels = @json($chartLabels);
    const data   = @json($chartData);
    new Chart(document.getElementById('chartPembayaran'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{ 
                label: 'Total Pembayaran (Rp)', 
                data: data, 
                backgroundColor: '#059669', // Emerald/Jade
                borderRadius: 8,
                barThickness: 28,
            }]
        },
        options: { 
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return 'Rp ' + Number(ctx.raw).toLocaleString('id-ID');
                        }
                    }
                }
            }, 
            scales: { 
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748B' } },
                y: { 
                    beginAtZero: true,
                    grid: { color: '#F1F5F9' },
                    ticks: { font: { size: 10 }, color: '#64748B' } 
                } 
            } 
        }
    });

    const tlabels = @json($tunggakanLabels);
    const tdata   = @json($tunggakanData);
    new Chart(document.getElementById('chartTunggakan'), {
        type: 'bar',
        data: {
            labels: tlabels,
            datasets: [{ 
                label: 'Jumlah Tagihan Overdue', 
                data: tdata, 
                backgroundColor: '#F43F5E', // Rose/Coral
                borderRadius: 8,
                barThickness: 28,
            }]
        },
        options: { 
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }, 
            scales: { 
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748B' } },
                y: { 
                    beginAtZero: true,
                    grid: { color: '#F1F5F9' },
                    ticks: { font: { size: 10 }, color: '#64748B' } 
                } 
            } 
        }
    });
    </script>
</x-app-layout>
