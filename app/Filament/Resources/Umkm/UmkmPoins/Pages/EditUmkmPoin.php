<?php

namespace App\Filament\Resources\Umkm\UmkmPoins\Pages;

use App\Filament\Resources\Umkm\UmkmPoins\UmkmPoinResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmPoin extends EditRecord
{
    protected static string $resource = UmkmPoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
