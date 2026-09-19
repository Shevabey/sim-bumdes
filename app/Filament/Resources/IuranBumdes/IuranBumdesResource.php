<?php

namespace App\Filament\Resources\IuranBumdes;

use App\Filament\Resources\IuranBumdes\Pages\CreateIuranBumdes;
use App\Filament\Resources\IuranBumdes\Pages\EditIuranBumdes;
use App\Filament\Resources\IuranBumdes\Pages\ListIuranBumdes;
use App\Filament\Resources\IuranBumdes\Schemas\IuranBumdesForm;
use App\Filament\Resources\IuranBumdes\Tables\IuranBumdesTable;
use App\Models\IuranBumdes;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IuranBumdesResource extends Resource
{
    protected static ?string $model = IuranBumdes::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id_iuran';

    public static function form(Schema $schema): Schema
    {
        return IuranBumdesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IuranBumdesTable::configure($table);
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
            'index' => ListIuranBumdes::route('/'),
            'create' => CreateIuranBumdes::route('/create'),
            'edit' => EditIuranBumdes::route('/{record}/edit'),
        ];
    }
}
