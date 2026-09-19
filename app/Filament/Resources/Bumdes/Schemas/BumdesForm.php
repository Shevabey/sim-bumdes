<?php

namespace App\Filament\Resources\Bumdes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BumdesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_kelurahan')
                    ->required(),
                TextInput::make('nama_bumdes')
                    ->required(),
                Toggle::make('status_aktif')
                    ->required(),
                DatePicker::make('tanggal_berdiri'),
            ]);
    }
}
