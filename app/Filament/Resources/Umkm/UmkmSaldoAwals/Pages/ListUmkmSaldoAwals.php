<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwals\Pages;

use App\Filament\Resources\Umkm\UmkmSaldoAwals\UmkmSaldoAwalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmSaldoAwals extends ListRecords
{
    protected static string $resource = UmkmSaldoAwalResource::class;
    protected static ?string $title = "Daftar Saldo Awal";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Saldo Awal"),
        ];
    }
}
