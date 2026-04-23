<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwals\Schemas;

use App\Models\DetailAkunKeuangan;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class UmkmSaldoAwalForm
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
                                        ->whereNull('idUsers') // Filter idUsers adalah null
                                        ->where('tipe', 0)      // Filter tipe adalah 0
                                        ->get()
                                        ->mapWithKeys(function ($record) {
                                            $namaAkun = $record->akunKeuangan->name ?? 'Akun Tidak Ditemukan';
                                            return [$record->id => "{$record->akunKeuangan->no_referensi} - {$namaAkun} (Kategori: {$record->akunKeuangan->category} - {$record->akunKeuangan->detail_category})"];
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
