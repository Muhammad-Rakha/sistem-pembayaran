<?php

namespace App\Exports;

use App\Models\Pembayaran;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanPembayaranExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function collection(): Enumerable
    {
        return Pembayaran::with('tagihan.siswa.kelas', 'transaksi')
            ->when($this->filters['tanggal_dari'] ?? null,   fn ($q) => $q->whereDate('tanggal_pembayaran', '>=', $this->filters['tanggal_dari']))
            ->when($this->filters['tanggal_sampai'] ?? null, fn ($q) => $q->whereDate('tanggal_pembayaran', '<=', $this->filters['tanggal_sampai']))
            ->when($this->filters['bulan'] ?? null, fn ($q) => $q->whereHas('tagihan', fn ($t) => $t->where('bulan', $this->filters['bulan'])))
            ->when($this->filters['tahun'] ?? null, fn ($q) => $q->whereHas('tagihan', fn ($t) => $t->where('tahun', $this->filters['tahun'])))
            ->when($this->filters['id_kelas'] ?? null, fn ($q) => $q->whereHas('tagihan.siswa', fn ($s) => $s->where('id_kelas', $this->filters['id_kelas'])))
            ->latest('tanggal_pembayaran')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Tanggal Bayar', 'NIS', 'Nama Siswa', 'Kelas', 'Bulan/Tahun', 'Nominal', 'Transaction ID', 'Status'];
    }

    public function map(mixed $row): array
    {
        static $no = 0;
        $no++;
        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return [
            $no,
            $row->tanggal_pembayaran->format('d/m/Y H:i'),
            $row->tagihan->siswa->nis ?? '-',
            $row->tagihan->siswa->nama ?? '-',
            $row->tagihan->siswa->kelas->nama_kelas ?? '-',
            ($namaBulan[$row->tagihan->bulan] ?? $row->tagihan->bulan) . ' ' . $row->tagihan->tahun,
            'Rp ' . number_format($row->nominal_pembayaran, 0, ',', '.'),
            $row->transaksi->transaction_id ?? '-',
            $row->status,
        ];
    }
}
