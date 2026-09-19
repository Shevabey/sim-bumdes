<?php

namespace App\Filament\Widgets;

use App\Models\IuranBumdes;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget: Iuran Menunggak
 * FR-30 — indikator otomatis BUMDes yang menunggak iuran.
 * Ditampilkan di Dashboard Filament untuk role nasional & koordinator.
 */
class IuranMenunggakWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    // Polling 60 detik agar dashboard terasa real-time (sesuai FR-29)
    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $bulanIni     = now()->format('Y-m');
        $totalBumdes  = \App\Models\Bumdes::where('status_aktif', true)->count();
        $sudahBayar   = IuranBumdes::where('bulan_tahun', $bulanIni)
                            ->where('status', 'lunas')
                            ->count();
        $menunggak    = IuranBumdes::where('bulan_tahun', $bulanIni)
                            ->where('status', 'belum_bayar')
                            ->count();

        return [
            Stat::make('BUMDes Aktif', $totalBumdes)
                ->description('Total BUMDes terdaftar & aktif')
                ->color('gray'),

            Stat::make('Iuran Bulan Ini — Lunas', $sudahBayar)
                ->description('BUMDes yang sudah membayar ' . now()->translatedFormat('F Y'))
                ->color('success'),

            Stat::make('Iuran Menunggak', $menunggak)
                ->description('BUMDes belum bayar ' . now()->translatedFormat('F Y'))
                ->color($menunggak > 0 ? 'danger' : 'success')
                ->icon($menunggak > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'),
        ];
    }
}
