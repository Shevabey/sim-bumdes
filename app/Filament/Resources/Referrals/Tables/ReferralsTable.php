<?php

namespace App\Filament\Resources\Referrals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReferralsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_referral')
                    ->searchable(),
                TextColumn::make('id_bumdes_pengaju')
                    ->searchable(),
                TextColumn::make('id_bumdes_penerima')
                    ->searchable(),
                TextColumn::make('kode_unik')
                    ->searchable(),
                TextColumn::make('tanggal_generate')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('tanggal_expired')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('tanggal_redeem')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('batas_verifikasi')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('tanggal_cair')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
