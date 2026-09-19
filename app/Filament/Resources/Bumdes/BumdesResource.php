<?php

namespace App\Filament\Resources\Bumdes;

use App\Filament\Resources\Bumdes\Pages\CreateBumdes;
use App\Filament\Resources\Bumdes\Pages\EditBumdes;
use App\Filament\Resources\Bumdes\Pages\ListBumdes;
use App\Filament\Resources\Bumdes\Schemas\BumdesForm;
use App\Filament\Resources\Bumdes\Tables\BumdesTable;
use App\Models\Bumdes;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BumdesResource extends Resource
{
    protected static ?string $model = Bumdes::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_bumdes';

    public static function form(Schema $schema): Schema
    {
        return BumdesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BumdesTable::configure($table);
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
            'index' => ListBumdes::route('/'),
            'create' => CreateBumdes::route('/create'),
            'edit' => EditBumdes::route('/{record}/edit'),
        ];
    }
}
