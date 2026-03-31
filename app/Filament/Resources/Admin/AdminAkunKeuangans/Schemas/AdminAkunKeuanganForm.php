<?php

namespace App\Filament\Resources\Admin\AdminAkunKeuangans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminAkunKeuanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make("name")
                    ->label("Nama Akun")
                    ->required(),
                Select::make('category')
                            ->label('Kategori')
                            ->options([
                                "aset" => 'Aset',
                                "pendapatan" => 'Pendapatan',
                                "beban_biaya" => 'Beban atau Biaya',
                                "modal" => 'Modal',
                                "kewajiban" => 'Kewajiban',
                                "other" => 'Pendapatan Lain Lain',
                            ])
                            ->required(),

                Select::make('detail_category')
                            ->label('Detail Kategori')
                            ->options([
                                "aset" => 'Aset Lancar',
                                "aset_tetap" => 'Aset Tetap',
                                "aset_tak_berwujud" => 'Aset Tak Berwujud',
                                "kewajiban_jangka_pendek" => 'Kewajiban Jangka Pendek',
                                "kewajiban_jangka_panjang" => 'Kewajiban Jangka Panjang',
                                "modal" => 'Modal',
                                "beban_biaya" => 'Beban atau Biaya',
                                "kewajiban" => 'Kewajiban',
                                "other" => 'Pendapatan Lain Lain',
                            ])
                            ->required()

                            
            ]);
    }
}
