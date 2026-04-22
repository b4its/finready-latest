<?php

namespace App\Filament\Resources\Umkm\UmkmAkunKeuangans\Pages;

use App\Filament\Resources\Umkm\UmkmAkunKeuangans\UmkmAkunKeuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmAkunKeuangan extends EditRecord
{
    protected static string $resource = UmkmAkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
