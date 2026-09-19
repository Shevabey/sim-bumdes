<?php

namespace App\Filament\Resources\IuranBumdes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IuranBumdesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_bumdes')
                    ->required(),
                TextInput::make('bulan_tahun')
                    ->required(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->default(50000.0),
                Select::make('status')
                    ->options(['belum_bayar' => 'Belum bayar', 'lunas' => 'Lunas'])
                    ->default('belum_bayar')
                    ->required(),
                Select::make('sumber_dana')
                    ->options(['kas' => 'Kas', 'luar_kas' => 'Luar kas']),
                DateTimePicker::make('tanggal_bayar'),
                TextInput::make('diverifikasi_oleh'),
            ]);
    }
}
