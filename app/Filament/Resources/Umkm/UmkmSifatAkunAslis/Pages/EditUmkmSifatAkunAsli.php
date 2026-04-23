<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Pages;

use App\Filament\Resources\Umkm\UmkmSifatAkunAslis\UmkmSifatAkunAsliResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmSifatAkunAsli extends EditRecord
{
    protected static string $resource = UmkmSifatAkunAsliResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
