<?php

namespace App\Filament\Resources\Bumdes\Pages;

use App\Filament\Resources\Bumdes\BumdesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBumdes extends EditRecord
{
    protected static string $resource = BumdesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
