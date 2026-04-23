<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\Tables;

use App\Models\SaldoAwal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UmkmSaldoAwalAslisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => 
                $query->where('tipe', 2) // Satpam utama: Harus tipe 2
                    ->where(function ($q) {
                        $q->where('idUsers', Auth::id()); // Boleh punya saya atau master
                    })
                    ->orderBy('created_at', 'desc')
                )
            
            ->columns([
                //

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
