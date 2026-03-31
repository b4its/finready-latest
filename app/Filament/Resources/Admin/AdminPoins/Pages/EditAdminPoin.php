<?php

namespace App\Filament\Resources\Admin\AdminPoins\Pages;

use App\Filament\Resources\Admin\AdminPoins\AdminPoinResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminPoin extends EditRecord
{
    protected static string $resource = AdminPoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
