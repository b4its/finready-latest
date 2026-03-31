<?php

namespace App\Filament\Resources\Admin\AdminPoins\Pages;

use App\Filament\Resources\Admin\AdminPoins\AdminPoinResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminPoins extends ListRecords
{
    protected static ?string $title = "Daftar Poin";
    protected static string $resource = AdminPoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
