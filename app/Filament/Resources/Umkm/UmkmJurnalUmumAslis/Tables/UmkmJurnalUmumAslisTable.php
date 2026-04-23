<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\Tables;

use App\Models\JurnalUmum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UmkmJurnalUmumAslisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => 
                        $query->where(function ($q) {
                            $q->where('idUsers', Auth::id())
                            ->orWhere('tipe', 2);
                        })->orderBy('created_at', 'desc')
                    )
            ->columns([
                //

                TextColumn::make('periode')
                    ->label('Tanggal')
                    ->date('m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('akunKeuangan.no_referensi')
                    ->label('No. Referensi')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('No. Referensi disalin!'),

                TextColumn::make('details.metode_pembayaran')
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
                    ->badge()
                    ->default('Balance')
                    ->color(fn (string $state): string => match ($state) {
                        'Balance' => 'success',
                        'Tidak Balance' => 'danger',
                        'Kosong' => 'gray',
                    }),
                // TextColumn::make('status')
                //     ->label('Status Balance')
                //     ->getStateUsing(function (JurnalUmum $record) {
                //         $debet = $record->details->where('is_debet', 'D')->sum('amount');
                //         $kredit = $record->details->where('is_debet', 'K')->sum('amount');
                        
                //         if ($debet === 0 && $kredit === 0) return 'Kosong';
                //         return $debet === $kredit ? 'Balance' : 'Tidak Balance';
                //     })
                //     ->badge()
                //     ->color(fn (string $state): string => match ($state) {
                //         'Balance' => 'success',
                //         'Tidak Balance' => 'danger',
                //         'Kosong' => 'gray',
                //     }),

                
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
