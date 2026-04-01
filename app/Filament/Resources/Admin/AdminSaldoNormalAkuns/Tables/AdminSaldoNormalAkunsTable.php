<?php

namespace App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Tables;

use App\Models\DetailAkunKeuangan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminSaldoNormalAkunsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                DetailAkunKeuangan::query()
                    // Pastikan nama tabel 'users' sesuai migrasi
                    ->selectRaw('detail_akun_keuangan.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),

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

                // Menambahkan format mata uang Rupiah
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id') 
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
