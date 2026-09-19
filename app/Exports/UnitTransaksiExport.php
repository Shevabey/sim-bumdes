<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UnitTransaksiExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(protected string $idUnit) {}

    public function title(): string
    {
        return "Transaksi Unit {$this->idUnit}";
    }

    public function collection()
    {
        return Transaksi::where('id_unit', $this->idUnit)
            ->orderBy('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
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
            strtoupper($row->tipe),
            number_format($row->jumlah, 2, ',', '.'),
            $row->tanggal?->format('d/m/Y') ?? '-',
            $row->keterangan ?? '-',
        ];
    }
}
