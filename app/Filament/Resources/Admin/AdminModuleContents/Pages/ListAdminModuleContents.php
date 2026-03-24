<?php

namespace App\Filament\Resources\Admin\AdminModuleContents\Pages;

use App\Filament\Resources\Admin\AdminModuleContents\AdminModuleContentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminModuleContents extends ListRecords
{
    protected static string $resource = AdminModuleContentResource::class;
    protected static ?string $title = "Daftar Modul Konten";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Modul Konten"),
        ];
    }
}
