<?php

namespace App\Filament\Resources\Admin\AdminJurnalUmums\Tables;

use App\Models\JurnalUmum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminJurnalUmumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                JurnalUmum::query()
                    // Pastikan nama tabel 'users' sesuai migrasi
                    ->selectRaw('jurnal_umum.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                    
                TextColumn::make('no_referensi')
                    ->label('No Referensi')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nama Pengguna')
                    ->sortable(),

                TextColumn::make('akunKeuangan.name')
                    ->label('Akun')
                    ->sortable(),

                TextColumn::make('kategori')
                    ->label('Kategori')
                    ->sortable(),
                    
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->sortable(),

                
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
