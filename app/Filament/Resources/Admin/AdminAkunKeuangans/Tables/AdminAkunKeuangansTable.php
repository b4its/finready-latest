<?php

namespace App\Filament\Resources\Admin\AdminAkunKeuangans\Tables;

use App\Models\AkunKeuangan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminAkunKeuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                AkunKeuangan::query()
                    // Pastikan nama tabel 'users' sesuai migrasi
                    ->selectRaw('akun_keuangan.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                TextColumn::make("name")
                    ->label("Nama Akun"),

                TextColumn::make("category")
                    ->label("Kategori"),
            ])
            ->filters([
                //
            ])
            ->emptyStateHeading('Tidak ada Data Akun Keuangan')
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
