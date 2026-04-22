<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Pages;

use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\UmkmSifatAkunKeuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmSifatAkunKeuangan extends EditRecord
{
    protected static string $resource = UmkmSifatAkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
