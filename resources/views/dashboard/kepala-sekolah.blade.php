<x-app-layout title="Dashboard Kepala Sekolah">
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <x-stat-card label="Siswa Aktif" :value="$totalSiswaAktif" color="blue" icon="🎓" />
        <x-stat-card label="Sudah Bayar (Bln Ini)" :value="$siswaSudahBayar" color="green" icon="✅" />
        <x-stat-card label="Belum Bayar (Bln Ini)" :value="$siswaBelumBayar" color="yellow" icon="⏳" />
        <x-stat-card label="Siswa Overdue" :value="$siswaOverdue" color="red" icon="⚠️" />
        <x-stat-card label="Total Diterima" :value="'Rp '.number_format($totalDiterima,0,',','.')" color="green" icon="💰" />
        <x-stat-card label="Total Tunggakan" :value="'Rp '.number_format($totalTunggakan,0,',','.')" color="red" icon="💸" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-4">Pembayaran per Bulan ({{ now()->year }})</h2>
            <canvas id="chartBayar" height="140"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
            <h2 class="font-semibold text-gray-700 mb-4">Tunggakan per Bulan</h2>
            <canvas id="chartTunggakan" height="140"></canvas>
        </div>
    </div>

    <script>
    new Chart(document.getElementById('chartBayar'), {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{ label: 'Nominal Pembayaran', data: @json($chartData), borderColor: '#3b82f6', fill: false, tension: 0.3 }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
    new Chart(document.getElementById('chartTunggakan'), {
        type: 'bar',
        data: {
            labels: @json($tunggakanLabels),
            datasets: [{ label: 'Jumlah Overdue', data: @json($tunggakanData), backgroundColor: '#f87171' }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    </script>
</x-app-layout>
