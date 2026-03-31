<?php

namespace App\Filament\Resources\Admin\AdminAkunKeuangans\Pages;

use App\Filament\Resources\Admin\AdminAkunKeuangans\AdminAkunKeuanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminAkunKeuangan extends EditRecord
{
    protected static string $resource = AdminAkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
