<?php

namespace App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\Pages;

use App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\UmkmPengajuanDataKeuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmPengajuanDataKeuangan extends EditRecord
{
    protected static string $resource = UmkmPengajuanDataKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
