<?php

namespace App\Filament\Resources\Admin\AdminSaldoAwals\Tables;

use App\Models\SaldoAwal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminSaldoAwalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                SaldoAwal::query()
                    ->selectRaw('saldo_awal.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('detailAkunKeuangan.akunKeuangan.no_referensi')
                    ->label('No Referensi Akun')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('debet')
                    ->label('Debet')
                    ->sortable(),
                TextColumn::make('kredit')
                    ->label('Kredit')
                    ->sortable(),

                
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->button()
                    ->color('danger') // default abu-abu (tidak merah)
                    ->requiresConfirmation() // pastikan tampil popup konfirmasi
                    ->modalHeading('Konfirmasi Hapus')
                    ->modalDescription('apakah yakin ingin menghapus data ini?')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
