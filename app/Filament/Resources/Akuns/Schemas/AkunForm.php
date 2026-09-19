<?php

namespace App\Filament\Resources\Akuns\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AkunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('username')
                    ->required(),
                TextInput::make('password_hash')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->options([
            'super_admin' => 'Super admin',
            'pengawas' => 'Pengawas',
            'penasihat' => 'Penasihat',
            'direktur' => 'Direktur',
            'admin_bumdes' => 'Admin bumdes',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'admin_unit' => 'Admin unit',
            'pengguna' => 'Pengguna',
        ])
                    ->required(),
                TextInput::make('id_bumdes'),
                TextInput::make('id_unit'),
                Toggle::make('status_aktif')
                    ->required(),
            ]);
    }
}
