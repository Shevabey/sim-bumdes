<?php

namespace App\Filament\Resources\Pelanggans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_unit')
                    ->required(),
                TextInput::make('nama')
                    ->required(),
                TextInput::make('kontak'),
                TextInput::make('id_akun'),
                Toggle::make('status_aktif')
                    ->required(),
            ]);
    }
}
