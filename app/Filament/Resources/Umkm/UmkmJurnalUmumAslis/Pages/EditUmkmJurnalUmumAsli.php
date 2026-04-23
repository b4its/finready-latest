<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\Pages;

use App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\UmkmJurnalUmumAsliResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmJurnalUmumAsli extends EditRecord
{
    protected static string $resource = UmkmJurnalUmumAsliResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
