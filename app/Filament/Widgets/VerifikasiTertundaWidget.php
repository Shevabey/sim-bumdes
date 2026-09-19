<?php

namespace App\Filament\Widgets;

use App\Models\Tagihan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget: Verifikasi Tertunda
 * FR-30 — peringatan otomatis untuk tagihan yang menunggu verifikasi lebih dari 24 jam.
 */
class VerifikasiTertundaWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $menungguVerifikasi = Tagihan::where('status', 'menunggu_verifikasi')->count();

        // Peringatan: tagihan menunggu verifikasi > 24 jam (target SLA PRD Bab 6)
        $lewat24Jam = Tagihan::where('status', 'menunggu_verifikasi')
            ->where('updated_at', '<', now()->subHours(24))
            ->count();

        $totalTagihanAktif = Tagihan::whereIn('status', ['belum_bayar', 'menunggu_verifikasi'])->count();

        return [
            Stat::make('Menunggu Verifikasi', $menungguVerifikasi)
                ->description('Bukti transfer diunggah, belum diverifikasi')
                ->color($menungguVerifikasi > 0 ? 'warning' : 'success')
                ->icon($menungguVerifikasi > 0 ? 'heroicon-o-clock' : 'heroicon-o-check-circle'),

            Stat::make('Lewat 24 Jam', $lewat24Jam)
                ->description('Verifikasi melebihi target SLA 24 jam')
                ->color($lewat24Jam > 0 ? 'danger' : 'success')
                ->icon($lewat24Jam > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'),

            Stat::make('Total Tagihan Aktif', $totalTagihanAktif)
                ->description('Tagihan belum lunas di seluruh unit')
                ->color('gray'),
        ];
    }
}
