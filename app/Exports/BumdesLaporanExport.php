<?php

namespace App\Exports;

use App\Models\Bumdes;
use App\Models\IuranBumdes;
use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class BumdesLaporanExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(
        protected string $idBumdes,
        protected string $bulanTahun // format: Y-m, contoh: 2026-09
    ) {}

    public function title(): string
    {
        return "Laporan BUMDes {$this->bulanTahun}";
    }

    public function collection()
    {
        return Transaksi::whereHas('unitUsaha', fn ($q) => $q->where('id_bumdes', $this->idBumdes))
            ->whereYear('tanggal', substr($this->bulanTahun, 0, 4))
            ->whereMonth('tanggal', substr($this->bulanTahun, 5, 2))
            ->orderBy('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Unit Usaha',
            'Tipe',
            'Jumlah (Rp)',
            'Tanggal',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id_transaksi,
            $row->unitUsaha?->nama_unit ?? '-',
            strtoupper($row->tipe),
            number_format($row->jumlah, 2, ',', '.'),
            $row->tanggal?->format('d/m/Y') ?? '-',
            $row->keterangan ?? '-',
        ];
    }
}
