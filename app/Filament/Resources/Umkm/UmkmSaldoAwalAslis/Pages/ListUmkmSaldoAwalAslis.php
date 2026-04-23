<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\Pages;

use App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\UmkmSaldoAwalAsliResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmSaldoAwalAslis extends ListRecords
{
    protected static string $resource = UmkmSaldoAwalAsliResource::class;

    protected static ?string $title = "Daftar Saldo Awal Riil";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambahkan Saldo Awal Riil")
                ->mutateFormDataUsing(function (array $data): array {;
                    $data['tipe'] = 2; // Indikator data praktek pengguna
                    return $data;
                })
        ];
    }
}
