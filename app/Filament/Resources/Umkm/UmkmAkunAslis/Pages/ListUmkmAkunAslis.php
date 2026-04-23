<?php

namespace App\Filament\Resources\Umkm\UmkmAkunAslis\Pages;

use App\Filament\Resources\Umkm\UmkmAkunAslis\UmkmAkunAsliResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmAkunAslis extends ListRecords
{
    protected static ?string $title = "Daftar Akun Keuangan Riil";
    protected static string $resource = UmkmAkunAsliResource::class;

protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambahkan Akun Keuangan Riil")
                ->mutateFormDataUsing(function (array $data): array {
                    $data['tipe'] = 2; // Indikator data praktek
                    return $data;
                })
        ];
    }
}
