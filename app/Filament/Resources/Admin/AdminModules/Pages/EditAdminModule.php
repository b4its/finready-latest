<?php

namespace App\Filament\Resources\Admin\AdminModules\Pages;

use App\Filament\Resources\Admin\AdminModules\AdminModuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminModule extends EditRecord
{
    protected static string $resource = AdminModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
