<?php

namespace App\Filament\Resources\Feedback\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FeedbackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('dari_id_akun')
                    ->required(),
                TextInput::make('ke_id_bumdes')
                    ->required(),
                TextInput::make('ke_id_unit'),
                Textarea::make('isi_catatan')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status_tindak_lanjut')
                    ->options(['belum' => 'Belum', 'sedang' => 'Sedang', 'selesai' => 'Selesai'])
                    ->default('belum')
                    ->required(),
                DateTimePicker::make('tanggal')
                    ->required(),
            ]);
    }
}
