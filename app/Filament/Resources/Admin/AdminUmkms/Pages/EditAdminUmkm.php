<?php

namespace App\Filament\Resources\Admin\AdminUmkms\Pages;

use App\Filament\Resources\Admin\AdminUmkms\AdminUmkmResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminUmkm extends EditRecord
{
    protected static string $resource = AdminUmkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
