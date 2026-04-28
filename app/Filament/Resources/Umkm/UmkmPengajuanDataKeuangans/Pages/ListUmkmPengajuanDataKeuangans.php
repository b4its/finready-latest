<?php

namespace App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\Pages;

use App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\UmkmPengajuanDataKeuanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmPengajuanDataKeuangans extends ListRecords
{
    protected static ?string $title = 'Pengajuan Data Keuangan';
    protected static string $resource = UmkmPengajuanDataKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
