<?php

namespace App\Filament\Resources\IuranBumdes\Pages;

use App\Filament\Resources\IuranBumdes\IuranBumdesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIuranBumdes extends ListRecords
{
    protected static string $resource = IuranBumdesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
