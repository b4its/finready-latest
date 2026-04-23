<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UmkmSifatAkunAsliForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('idUsers')
                    ->default(Auth::user()->id),

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