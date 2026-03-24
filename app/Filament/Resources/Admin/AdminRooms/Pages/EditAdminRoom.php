<?php

namespace App\Filament\Resources\Admin\AdminRooms\Pages;

use App\Filament\Resources\Admin\AdminRooms\AdminRoomResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminRoom extends EditRecord
{
    protected static string $resource = AdminRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
