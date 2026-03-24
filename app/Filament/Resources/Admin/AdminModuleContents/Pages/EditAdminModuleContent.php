<?php

namespace App\Filament\Resources\Admin\AdminModuleContents\Pages;

use App\Filament\Resources\Admin\AdminModuleContents\AdminModuleContentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminModuleContent extends EditRecord
{
    protected static string $resource = AdminModuleContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
