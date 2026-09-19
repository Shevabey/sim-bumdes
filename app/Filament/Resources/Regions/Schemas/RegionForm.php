<?php

namespace App\Filament\Resources\Regions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RegionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('level')
                    ->options([
            'provinsi' => 'Provinsi',
            'kota' => 'Kota',
            'kecamatan' => 'Kecamatan',
            'kelurahan' => 'Kelurahan',
        ])
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'id_region'),
                TextInput::make('nama')
                    ->required(),
                Toggle::make('is_koordinator')
                    ->required(),
            ]);
    }
}
