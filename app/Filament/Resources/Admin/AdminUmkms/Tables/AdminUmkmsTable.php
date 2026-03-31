<?php

namespace App\Filament\Resources\Admin\AdminUmkms\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminUmkmsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    // Pastikan nama tabel 'users' sesuai migrasi
                    ->selectRaw('users.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->where('role', 'umkm') // Exclude super admin from the list
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),
                TextColumn::make("name"),
                TextColumn::make("email"),
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
            ->emptyStateHeading('Tidak ada Data Akun UMKM')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
