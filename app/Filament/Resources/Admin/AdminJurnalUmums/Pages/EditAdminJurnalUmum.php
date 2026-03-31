<?php

namespace App\Filament\Resources\Admin\AdminJurnalUmums\Pages;

use App\Filament\Resources\Admin\AdminJurnalUmums\AdminJurnalUmumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminJurnalUmum extends EditRecord
{
    protected static string $resource = AdminJurnalUmumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
