<?php

namespace App\Filament\Resources\Umkm\UmkmAkunKeuangans\Pages;

use App\Filament\Resources\Umkm\UmkmAkunKeuangans\UmkmAkunKeuanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmAkunKeuangans extends ListRecords
{
    protected static string $resource = UmkmAkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Akun Keuangan")
            ->mutateFormDataUsing(function (array $data): array {
                    $data['tipe'] = 2;
                    return $data;
                }),
        ];
    }
}
