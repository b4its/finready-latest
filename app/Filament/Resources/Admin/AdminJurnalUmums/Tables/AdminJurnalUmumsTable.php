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

                    
                TextColumn::make('periode')
                    ->label('Tanggal')
                    ->date('m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('details.akunKeuangan.no_referensi')
                    ->label('No. Referensi')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('No. Referensi disalin!'),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tunai' => 'success',
                        'Kredit' => 'warning',
                        'Transfer' => 'info',
                        default => 'gray',
                    }),

                // Kalkulasi reaktif langsung di kolom tabel untuk Filament 5.x
                TextColumn::make('total_debet')
                    ->label('Total Debet')
                    ->getStateUsing(fn (JurnalUmum $record): float => (float) $record->details->where('is_debet', 'D')->sum('amount'))
                    ->money('IDR', locale: 'id')
                    ->alignRight(),

                TextColumn::make('total_kredit')
                    ->label('Total Kredit')
                    ->getStateUsing(fn (JurnalUmum $record): float => (float) $record->details->where('is_debet', 'K')->sum('amount'))
                    ->money('IDR', locale: 'id')
                    ->alignRight(),

                TextColumn::make('status')
                    ->label('Status Balance')
                    ->getStateUsing(function (JurnalUmum $record) {
                        $debet = $record->details->where('is_debet', 'D')->sum('amount');
                        $kredit = $record->details->where('is_debet', 'K')->sum('amount');
                        
                        if ($debet === 0 && $kredit === 0) return 'Kosong';
                        return $debet === $kredit ? 'Balance' : 'Tidak Balance';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Balance' => 'success',
                        'Tidak Balance' => 'danger',
                        'Kosong' => 'gray',
                    }),

                
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
