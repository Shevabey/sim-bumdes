<?php

namespace App\Filament\Resources\Bumdes\Pages;

use App\Filament\Resources\Bumdes\BumdesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBumdes extends ListRecords
{
    protected static string $resource = BumdesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
