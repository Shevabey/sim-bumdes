<?php

namespace App\Filament\Resources\UnitUsahas;

use App\Filament\Resources\UnitUsahas\Pages\CreateUnitUsaha;
use App\Filament\Resources\UnitUsahas\Pages\EditUnitUsaha;
use App\Filament\Resources\UnitUsahas\Pages\ListUnitUsahas;
use App\Filament\Resources\UnitUsahas\Schemas\UnitUsahaForm;
use App\Filament\Resources\UnitUsahas\Tables\UnitUsahasTable;
use App\Models\UnitUsaha;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UnitUsahaResource extends Resource
{
    protected static ?string $model = UnitUsaha::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_unit';

    public static function form(Schema $schema): Schema
    {
        return UnitUsahaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UnitUsahasTable::configure($table);
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
            'index' => ListUnitUsahas::route('/'),
            'create' => CreateUnitUsaha::route('/create'),
            'edit' => EditUnitUsaha::route('/{record}/edit'),
        ];
    }
}
