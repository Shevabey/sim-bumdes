<?php

namespace App\Filament\Resources\UnitUsahas\Pages;

use App\Filament\Resources\UnitUsahas\UnitUsahaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUnitUsaha extends EditRecord
{
    protected static string $resource = UnitUsahaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
