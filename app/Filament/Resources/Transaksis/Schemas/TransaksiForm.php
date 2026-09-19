<?php

namespace App\Filament\Resources\Transaksis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransaksiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_unit')
                    ->required(),
                Select::make('tipe')
                    ->options(['input' => 'Input', 'output' => 'Output'])
                    ->required(),
                TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                TextInput::make('detail'),
                DatePicker::make('tanggal')
                    ->required(),
                TextInput::make('dicatat_oleh')
                    ->required(),
            ]);
    }
}
