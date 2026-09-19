<?php

namespace App\Filament\Resources\Tagihans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TagihanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_pelanggan')
                    ->required(),
                TextInput::make('id_unit')
                    ->required(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                DatePicker::make('jatuh_tempo')
                    ->required(),
                Select::make('status')
                    ->options([
            'belum_bayar' => 'Belum bayar',
            'menunggu_verifikasi' => 'Menunggu verifikasi',
            'lunas' => 'Lunas',
            'ditolak' => 'Ditolak',
        ])
                    ->default('belum_bayar')
                    ->required(),
                Select::make('metode')
                    ->options(['tunai' => 'Tunai', 'transfer' => 'Transfer']),
                TextInput::make('bukti_transfer_url')
                    ->url(),
                TextInput::make('diverifikasi_oleh'),
                DateTimePicker::make('tanggal_verifikasi'),
            ]);
    }
}
