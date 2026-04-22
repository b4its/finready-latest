<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Tables;

use App\Models\DetailAkunKeuangan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UmkmSifatAkunKeuangansTable
{
    public static function configure(Table $table): Table
    {
            return $table
            // PERBAIKAN: modifyQueryUsing dipanggil langsung pada object $table
            ->modifyQueryUsing(fn ($query) => 
                $query->where('idUsers', Auth::id())
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //


                // Menampilkan No Referensi dari relasi AkunKeuangan
                TextColumn::make('akunKeuangan.no_referensi')
                    ->label('No. Referensi')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // Diperbaiki dari details.name menjadi akunKeuangan.name sesuai nama relasi di Model
                TextColumn::make('akunKeuangan.name')
                    ->label('Nama Akun')
                    ->searchable()
                    ->sortable(),
                TextColumn::make("is_debet")
                    ->label("Posisi")
                    ->formatStateUsing(fn (string $state): string => [
                        "D" => 'Debet',
                        "K" => 'Kredit',
                    ][$state] ?? $state),

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
