<?php

namespace App\Filament\Resources\Admin\AdminRooms\Pages;

use App\Filament\Resources\Admin\AdminRooms\AdminRoomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminRooms extends ListRecords
{
    protected static ?string $title = "Daftar Kuis";
    protected static string $resource = AdminRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Kuis"),
        ];
    }
}
