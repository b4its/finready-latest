<?php

namespace App\Filament\Resources\Umkm\UmkmAkunAslis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UmkmAkunAslisTable
{
public static function configure(Table $table): Table
    {
            return $table
            ->modifyQueryUsing(fn ($query) => 
                $query->whereIn('tipe', [0, 2]) // 1. Kunci hanya boleh tipe 0 dan 1
                    ->where(function ($q) {
                        $q->where('idUsers', Auth::id()) // 2. Milik user yang login
                            ->orWhereNull('idUsers');      // 3. ATAU yang master (null)
                    })
                    ->orderBy('idUsers', 'desc') // Prioritaskan yang ada idUsers-nya di atas
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make("no_referensi")
                    ->label("No Referensi"),

                TextColumn::make("name")
                    ->label("Nama Akun")
                    ->searchable(),
                

                TextColumn::make("category")
                    ->label("Kategori")
                    ->formatStateUsing(fn (string $state): string => [
                        "aset" => 'Aset',
                        "pendapatan" => 'Pendapatan',
                        "beban_biaya" => 'Beban atau Biaya',
                        "modal" => 'Modal',
                        "kewajiban" => 'Kewajiban',
                        "lain-lain" => 'Pendapatan Lain Lain',
                    ][$state] ?? $state),
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

