<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwals\Pages;

use App\Filament\Resources\Umkm\UmkmSaldoAwals\UmkmSaldoAwalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmSaldoAwal extends EditRecord
{
    protected static string $resource = UmkmSaldoAwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
