<?php

namespace App\Filament\Resources\Admin\AdminSaldoAwals\Schemas;

use App\Models\DetailAkunKeuangan;
use App\Models\DetailSaldoAwal;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class AdminSaldoAwalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Saldo Awal')
                    ->schema([
                        Select::make('idDetailAkunKeuangan')
                            ->label('Referensi Akun Keuangan')
                            ->options(function () {
                                return DetailAkunKeuangan::with('akunKeuangan')
                                    ->get()
                                    ->mapWithKeys(function ($record) {
                                        $namaAkun = $record->akunKeuangan->name ?? 'Akun Tidak Ditemukan';
                                        return [$record->id => "{$namaAkun} (ID: {$record->id})"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                                TextInput::make('debet')
                                    ->label('Debet')
                                    ->prefix('Rp')
                                    // Gunakan mask untuk tampilan ribuan yang cantik
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->required()
                                    ->live(onBlur: true),

                                TextInput::make('kredit')
                                    ->label('Kredit')
                                    ->prefix('Rp')
                                    // Gunakan mask untuk tampilan ribuan yang cantik
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->required()
                                    ->live(onBlur: true),
       
                    ])
                    ->columnSpanFull(),
            ]);
    }
}