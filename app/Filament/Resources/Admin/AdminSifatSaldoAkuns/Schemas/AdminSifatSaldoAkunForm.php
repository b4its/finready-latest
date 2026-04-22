<?php

namespace App\Filament\Resources\Admin\AdminSifatSaldoAkuns\Schemas;


use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;


class AdminSifatSaldoAkunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Select::make('idUsers')
                    ->label('Pilih Pengguna')
                    ->relationship(
                        name: 'user', 
                        titleAttribute: 'name',
                        // Filter: Hanya tampilkan user dengan role umkm
                        modifyQueryUsing: fn ($query) => $query->where('role', 'umkm') 
                    )
                    ->searchable()
                    ->preload()
                    ->live(),

                Select::make('idAkunKeuangan')
                    ->relationship('akunKeuangan', 'name')
                    ->label('Pilih Akun Keuangan')
                    // Memodifikasi tampilan dropdown agar menampilkan No Referensi & Nama Akun
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->no_referensi} - {$record->name}")
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('is_debet')
                    ->label('Posisi')
                    ->options([
                        "D" => 'Debet',
                        "K" => 'Kredit',
                    ])
                    ->live() 
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
