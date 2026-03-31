<?php

namespace App\Filament\Resources\Admin\AdminJurnalUmums\Pages;

use App\Filament\Resources\Admin\AdminJurnalUmums\AdminJurnalUmumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminJurnalUmums extends ListRecords
{
    protected static ?string $title = "Daftar Jurnal Umum";
    protected static string $resource = AdminJurnalUmumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Jurnal Umum"),
        ];
    }
}
