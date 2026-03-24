<?php

namespace App\Filament\Resources\Admin\AdminModules\Pages;

use App\Filament\Resources\Admin\AdminModules\AdminModuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminModules extends ListRecords
{
    protected static string $resource = AdminModuleResource::class;
    protected static ?string $title = 'Daftar Modul Materi';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambahkan Modul'),
        ];
    }
}
