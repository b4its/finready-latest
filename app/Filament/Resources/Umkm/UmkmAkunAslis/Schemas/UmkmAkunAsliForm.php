<?php

namespace App\Filament\Resources\Umkm\UmkmAkunAslis\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class UmkmAkunAsliForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Hidden::make('idUsers')
                    ->default(Auth::user()->id),

                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        "aset" => 'Aset',
                        "pendapatan" => 'Pendapatan',
                        "beban_biaya" => 'Beban atau Biaya',
                        "modal" => 'Modal',
                        "kewajiban" => 'Kewajiban',
                        "lain-lain" => 'Pendapatan Lain Lain',
                    ])
                    ->required()
                    ->live(onBlur: false) // Eksekusi instan khas V5
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if (!$state) {
                            $set('no_referensi', null);
                            return;
                        }

                        // Match expression yang dioptimalkan untuk Laravel 13/PHP 8.4+
                        $prefix = match ($state) {
                            'aset' => '1',
                            'pendapatan' => '4',
                            'beban_biaya' => '5',
                            'modal' => '3',
                            'kewajiban' => '2',
                            'lain-lain' => '6',
                            default => '0',
                        };

                        // Generate 4 angka random dengan format 0000 - 9999
                        $set('no_referensi', sprintf('%s-%04d', $prefix, mt_rand(0, 9999)));
                    }),

                    Select::make('detail_category')
                        ->label('Detail Kategori')
                        ->options([
                            'aset'                     => 'Aset Lancar',
                            'aset_tetap'               => 'Aset Tetap',
                            'aset_tak_berwujud'        => 'Aset Tak Berwujud',
                            'kewajiban_jangka_pendek'  => 'Kewajiban Jangka Pendek',
                            'kewajiban_jangka_panjang' => 'Kewajiban Jangka Panjang',
                            'modal'                    => 'Modal',
                            'beban_biaya'              => 'Beban atau Biaya',
                            'kewajiban'                => 'Kewajiban',
                            'pendapatan'               => 'Pendapatan',
                            'lain-lain'                => 'Pendapatan Lain Lain',
                        ])
                        ->required(),

                    TextInput::make('no_referensi')
                        ->label('No. Referensi')
                        ->required()
                        ->readOnly()
                        ->unique(ignoreRecord: true),

                    TextInput::make('name')
                        ->label('Nama Akun')
                        ->required()
                        ->maxLength(255),
            ]);
    }
}