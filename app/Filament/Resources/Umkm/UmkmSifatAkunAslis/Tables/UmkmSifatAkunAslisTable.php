<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UmkmSifatAkunAslisTable
{
public static function configure(Table $table): Table
    {
            return $table
            // PERBAIKAN: modifyQueryUsing dipanggil langsung pada object $table
            ->modifyQueryUsing(fn ($query) => 
                $query->where('tipe', 2) // Satpam utama: Harus tipe 2
                    ->where(function ($q) {
                        $q->where('idUsers', Auth::id()); // Boleh punya saya atau master
                    })
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

