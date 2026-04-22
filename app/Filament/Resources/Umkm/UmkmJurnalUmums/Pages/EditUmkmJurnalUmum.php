<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmums\Pages;

use App\Filament\Resources\Umkm\UmkmJurnalUmums\UmkmJurnalUmumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmJurnalUmum extends EditRecord
{
    protected static string $resource = UmkmJurnalUmumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
