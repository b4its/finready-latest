<?php

namespace App\Filament\Resources\Umkm\UmkmAkunAslis\Pages;

use App\Filament\Resources\Umkm\UmkmAkunAslis\UmkmAkunAsliResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmAkunAsli extends EditRecord
{
    protected static string $resource = UmkmAkunAsliResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
