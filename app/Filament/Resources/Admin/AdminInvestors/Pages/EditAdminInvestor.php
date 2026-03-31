<?php

namespace App\Filament\Resources\Admin\AdminInvestors\Pages;

use App\Filament\Resources\Admin\AdminInvestors\AdminInvestorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminInvestor extends EditRecord
{
    protected static string $resource = AdminInvestorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
