<?php

namespace App\Filament\Resources\Investor\InvestorUmkmLists\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class InvestorUmkmListsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                        // Pastikan nama tabel 'users' sesuai migrasi
                        ->selectRaw('users.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                        ->where('role', 'umkm')
                        ->orderBy('created_at', 'desc')
            )
            ->columns([
                //
                TextColumn::make('name')
                    ->label('Nama'),
                TextColumn::make('umkmProfile.jenisUsaha')
                    ->label('Jenis Usaha'),
                TextColumn::make('umkmProfile.name')
                    ->label('Nama    Usaha'),
                TextColumn::make('umkmProfile.level')
                    ->label('Indikator')
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make('detail_umkm')
                    ->label('Detail UMKM')
                    ->icon('heroicon-o-building-storefront')
                    ->color('info')
                    // Opsional: Hanya tampilkan tombol ini jika user memiliki role umkm dan data profilnya ada
                    ->visible(fn (Model $record): bool => $record->role === 'umkm' && $record->umkmProfile !== null)
                    ->infolist([
                        Section::make('Informasi Pemilik (User)')
                            ->schema([
                                TextEntry::make('name')->label('Nama Akun'),
                                TextEntry::make('email')->label('Email Terdaftar'),
                            ])->columns(2),

                        // Menggunakan ->relationship() agar konteks data berpindah ke tabel umkm_profile
                        Section::make('Informasi Detail UMKM')
                            ->relationship('umkmProfile')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nama Usaha')
                                    ->weight('bold'),
                                TextEntry::make('jenisUsaha')
                                    ->label('Jenis Usaha'),
                                TextEntry::make('nib')
                                    ->label('NIB')
                                    ->copyable(), // Memudahkan user menyalin NIB
                                TextEntry::make('level')
                                    ->label('Status Level')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'learning' => 'warning',
                                        'verified' => 'success', // Sesuaikan dengan enum/level riil Anda
                                        default => 'gray',
                                    }),
                                TextEntry::make('email')
                                    ->label('Email Usaha')
                                    ->icon('heroicon-m-envelope'),
                                TextEntry::make('phone')
                                    ->label('Nomor Telepon')
                                    ->icon('heroicon-m-phone'),
                                TextEntry::make('modal_awal')
                                    ->label('Modal Awal')
                                    ->numeric()
                                    ->prefix('Rp '),
                                TextEntry::make('alamat')
                                    ->label('Alamat Usaha')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // Menampilkan daftar Sosial Media menggunakan dot-notation relasi bertingkat
                        Section::make('Sosial Media')
                            ->schema([
                                RepeatableEntry::make('umkmProfile.sosialMedia')
                                    ->label('')
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Platform')
                                            ->icon('heroicon-m-globe-alt'),
                                        TextEntry::make('link')
                                            ->label('URL')
                                            ->url(fn ($state) => $state)
                                            ->openUrlInNewTab()
                                            ->color('primary'),
                                    ])
                                    ->columns(2)
                            ])
                            // Menyembunyikan section ini jika UMKM belum mengisi sosial media satupun
                            ->visible(fn (Model $record): bool => $record->umkmProfile?->sosialMedia->count() > 0),
                    ])->modalWidth('3xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
