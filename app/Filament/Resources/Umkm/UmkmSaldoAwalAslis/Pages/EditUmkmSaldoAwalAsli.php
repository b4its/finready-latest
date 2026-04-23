<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\Pages;

use App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\UmkmSaldoAwalAsliResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmSaldoAwalAsli extends EditRecord
{
    protected static string $resource = UmkmSaldoAwalAsliResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
