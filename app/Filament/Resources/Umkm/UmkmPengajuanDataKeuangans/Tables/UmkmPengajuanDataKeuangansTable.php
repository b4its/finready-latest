<?php

namespace App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\Tables;

use App\Models\PengajuanDataKeuangan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UmkmPengajuanDataKeuangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                PengajuanDataKeuangan::query()
                    ->selectRaw('pengajuan_data_keuangan.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->where('umkm_target', Auth::user()->id)
                    ->orderBy('created_at', 'desc') // urutkan tampilannya dari terbaru
            )
            ->columns([
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Nama Investor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(20) // Membatasi tampilan menjadi 50 karakter
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState()) // Opsional: Munculkan teks lengkap saat kursor diarahkan ke teks
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Alasan Pengajuan')
                    ->limit(20) // Membatasi tampilan menjadi 50 karakter
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState()) // Opsional: Munculkan teks lengkap saat kursor diarahkan ke teks
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status_pengajuan')
                    ->label('Status Pengajuan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '0' => 'Tidak Diterima',
                        '1' => 'Diterima',
                        default => 'Draft',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('lihatDetail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Detail Pengajuan Keuangan')
                    ->modalSubmitAction(false) // Menghilangkan tombol submit karena hanya melihat
                    ->modalCancelActionLabel('Tutup')
                    ->infolist([
                        Section::make('Informasi Pengajuan')
                            ->schema([
                                TextEntry::make('row_num')
                                    ->label('Nomor Urut'),
                                TextEntry::make('umkmTarget.name')
                                    ->label('Nama Investor'),
                                TextEntry::make('title')
                                    ->label('Judul Pengajuan'),
                                TextEntry::make('status_pengajuan')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        '0' => 'Tidak Diterima',
                                        '1' => 'Diterima',
                                        default => 'Draft',
                                    })
                                    ->color(fn (string $state): string => match ($state) {
                                        '0' => 'danger',
                                        '1' => 'success',
                                        default => 'gray',
                                    }),
                                TextEntry::make('created_at')
                                    ->label('Tanggal Pengajuan')
                                    ->dateTime('d M Y H:i'),
                                TextEntry::make('keterangan')
                                    ->label('Alasan Pengajuan')
                                    ->columnSpanFull() // Mengambil seluruh lebar baris
                                    ->markdown(), // Jika keterangan berisi format teks/markdown
                            ])->columns(2), // Membagi tampilan menjadi 2 kolom
                    ]),
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    // Tombol akan tersembunyi jika status_pengajuan bernilai 1
                    ->hidden(fn (PengajuanDataKeuangan $record) => $record->status_pengajuan == 1)
                    ->action(function (PengajuanDataKeuangan $record) {
                        $record->update([
                            'status_pengajuan' => 1,
                        ]);
                        
                        Notification::make()
                            ->title('Status Pengajuan Berhasil Disetujui')
                            ->success()
                            ->send();
                    }),
                Action::make('batalkan')
                    ->label('Batalkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    // Tombol akan tersembunyi jika status_pengajuan bernilai 1
                    ->hidden(fn (PengajuanDataKeuangan $record) => $record->status_pengajuan == 0)
                    ->action(function (PengajuanDataKeuangan $record) {
                        $record->update([
                            'status_pengajuan' => 0,
                        ]);
                        
                        Notification::make()
                            ->title('Status Pengajuan Berhasil Dibatalkan')
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
