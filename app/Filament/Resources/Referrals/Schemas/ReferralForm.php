<?php

namespace App\Filament\Resources\Referrals\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReferralForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_bumdes_pengaju')
                    ->required(),
                TextInput::make('id_bumdes_penerima'),
                TextInput::make('kode_unik')
                    ->required(),
                DateTimePicker::make('tanggal_generate')
                    ->required(),
                DateTimePicker::make('tanggal_expired')
                    ->required(),
                Select::make('status')
                    ->options([
            'aktif' => 'Aktif',
            'terpakai' => 'Terpakai',
            'kedaluwarsa' => 'Kedaluwarsa',
            'pending' => 'Pending',
            'cair' => 'Cair',
            'gagal' => 'Gagal',
        ])
                    ->default('aktif')
                    ->required(),
                DateTimePicker::make('tanggal_redeem'),
                DateTimePicker::make('batas_verifikasi'),
                DateTimePicker::make('tanggal_cair'),
            ]);
    }
}
