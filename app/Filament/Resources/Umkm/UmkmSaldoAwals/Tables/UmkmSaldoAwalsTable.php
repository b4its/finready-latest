<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwals\Tables;

use App\Models\SaldoAwal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UmkmSaldoAwalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                SaldoAwal::query()
                    ->selectRaw('saldo_awal.*, ROW_NUMBER() OVER (ORDER BY created_at DESC) as row_num')
                    ->where(function ($query) {
                        $query->where('idUsers', Auth::id())
                            ->orWhereNull('idUsers');
                    })
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
