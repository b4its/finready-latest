<?php

namespace App\Filament\Resources\Admin\AdminSifatSaldoAkuns\Pages;

use App\Filament\Resources\Admin\AdminSifatSaldoAkuns\AdminSifatSaldoAkunResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminSifatSaldoAkun extends EditRecord
{
    protected static string $resource = AdminSifatSaldoAkunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
