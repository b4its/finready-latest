<?php

namespace App\Filament\Resources\Admin\AdminSaldoAwals\Pages;

use App\Filament\Resources\Admin\AdminSaldoAwals\AdminSaldoAwalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminSaldoAwal extends EditRecord
{
    protected static string $resource = AdminSaldoAwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
