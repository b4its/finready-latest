<?php

namespace App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Pages;

use App\Filament\Resources\Admin\AdminSaldoNormalAkuns\AdminSaldoNormalAkunResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminSaldoNormalAkun extends EditRecord
{
    protected static string $resource = AdminSaldoNormalAkunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
