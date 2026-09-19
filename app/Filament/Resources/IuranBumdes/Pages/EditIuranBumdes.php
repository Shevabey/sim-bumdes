<?php

namespace App\Filament\Resources\IuranBumdes\Pages;

use App\Filament\Resources\IuranBumdes\IuranBumdesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIuranBumdes extends EditRecord
{
    protected static string $resource = IuranBumdesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
