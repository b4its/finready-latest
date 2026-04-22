<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmums\Pages;

use App\Filament\Resources\Umkm\UmkmJurnalUmums\UmkmJurnalUmumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmJurnalUmums extends ListRecords
{
    protected static string $resource = UmkmJurnalUmumResource::class;
    protected static ?string $title = "Daftar Jurnal Umum";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Jurnal Umum")
            ->mutateFormDataUsing(function (array $data): array {
                    $data['tipe'] = 2;
                    return $data;
                }),
        ];
    }
}
