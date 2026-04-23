<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Pages;

use App\Filament\Resources\Umkm\UmkmSifatAkunAslis\UmkmSifatAkunAsliResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmSifatAkunAslis extends ListRecords
{
    protected static string $resource = UmkmSifatAkunAsliResource::class;

    protected static ?string $title = 'Daftar Sifat Akun Keuangan Riil';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambahkan Sifat Akun Keuangan Riil")
                ->mutateFormDataUsing(function (array $data): array {
                    $data['tipe'] = 2; 
                    return $data;
                })
        ];
    }
}
