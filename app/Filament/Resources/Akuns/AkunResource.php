<?php

namespace App\Filament\Resources\Akuns;

use App\Filament\Resources\Akuns\Pages\CreateAkun;
use App\Filament\Resources\Akuns\Pages\EditAkun;
use App\Filament\Resources\Akuns\Pages\ListAkuns;
use App\Filament\Resources\Akuns\Schemas\AkunForm;
use App\Filament\Resources\Akuns\Tables\AkunsTable;
use App\Models\Akun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AkunResource extends Resource
{
    protected static ?string $model = Akun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return AkunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AkunsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAkuns::route('/'),
            'create' => CreateAkun::route('/create'),
            'edit' => EditAkun::route('/{record}/edit'),
        ];
    }
}
