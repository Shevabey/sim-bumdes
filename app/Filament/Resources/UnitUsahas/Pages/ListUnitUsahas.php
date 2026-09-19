<?php

namespace App\Filament\Resources\UnitUsahas\Pages;

use App\Filament\Resources\UnitUsahas\UnitUsahaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitUsahas extends ListRecords
{
    protected static string $resource = UnitUsahaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
