<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use App\Models\UnitUsaha;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget: Unit Tanpa Aktivitas
 * FR-30 — peringatan otomatis unit aktif yang tidak ada transaksi dalam 30 hari.
 */
class UnitTanpaAktivitasWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $totalUnit = UnitUsaha::where('status_aktif', true)->count();

        // Unit aktif yang punya transaksi dalam 30 hari terakhir
        $unitAktifBulanIni = Transaksi::where('tanggal', '>=', now()->subDays(30))
            ->distinct('id_unit')
            ->count('id_unit');

        $unitTanpaAktivitas = max(0, $totalUnit - $unitAktifBulanIni);

        // Referral pending (sedang menunggu syarat aktivitas terpenuhi)
        $referralPending = \App\Models\Referral::where('status', 'pending')->count();

        return [
            Stat::make('Unit Aktif', $totalUnit)
                ->description('Total unit usaha berstatus aktif')
                ->color('gray'),

            Stat::make('Tanpa Aktivitas 30 Hari', $unitTanpaAktivitas)
                ->description('Unit aktif tanpa transaksi selama 30 hari')
                ->color($unitTanpaAktivitas > 0 ? 'warning' : 'success')
                ->icon($unitTanpaAktivitas > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle'),

            Stat::make('Referral Pending', $referralPending)
                ->description('Kode referral sedang menunggu verifikasi aktivitas')
                ->color($referralPending > 0 ? 'warning' : 'gray'),
        ];
    }
}
