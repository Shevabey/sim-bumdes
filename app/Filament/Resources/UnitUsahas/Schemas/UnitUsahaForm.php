<?php

namespace App\Filament\Resources\UnitUsahas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UnitUsahaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_bumdes')
                    ->required(),
                Select::make('jenis_unit')
                    ->options([
            'pamdes' => 'Pamdes',
            'peternakan' => 'Peternakan',
            'mitra_tani' => 'Mitra tani',
            'sewa_mobil' => 'Sewa mobil',
            'sampah' => 'Sampah',
            'custom' => 'Custom',
        ])
                    ->required(),
                TextInput::make('nama_unit')
                    ->required(),
                TextInput::make('skema_field'),
                Toggle::make('status_aktif')
                    ->required(),
            ]);
    }
}
