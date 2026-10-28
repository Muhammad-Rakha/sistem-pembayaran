<?php

namespace App\Exports;

use App\Models\TagihanSpp;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanTunggakanExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function collection(): Enumerable
    {
        return TagihanSpp::with('siswa.kelas')
            ->where('status', 'OVERDUE')
            ->when($this->filters['bulan'] ?? null, fn ($q) => $q->where('bulan', $this->filters['bulan']))
            ->when($this->filters['tahun'] ?? null, fn ($q) => $q->where('tahun', $this->filters['tahun']))
            ->when($this->filters['id_kelas'] ?? null, fn ($q) => $q->whereHas('siswa', fn ($s) => $s->where('id_kelas', $this->filters['id_kelas'])))
            ->orderBy('tahun')->orderBy('bulan')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Bulan Tunggakan', 'Tahun', 'Nominal', 'Jatuh Tempo', 'Status'];
    }

    public function map(mixed $row): array
    {
        static $no = 0;
        $no++;
        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return [
            $no,
            $row->siswa->nis ?? '-',
            $row->siswa->nama ?? '-',
            $row->siswa->kelas->nama_kelas ?? '-',
            $namaBulan[$row->bulan] ?? $row->bulan,
            $row->tahun,
            'Rp ' . number_format($row->nominal, 0, ',', '.'),
            $row->tanggal_jatuh_tempo->format('d/m/Y'),
            'OVERDUE',
        ];
    }
}
