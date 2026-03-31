<?php

namespace App\Filament\Resources\Admin\AdminAkunKeuangans\Pages;

use App\Filament\Resources\Admin\AdminAkunKeuangans\AdminAkunKeuanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminAkunKeuangans extends ListRecords
{
    protected static ?string $title = "Daftar Akun Keuangan";
    protected static string $resource = AdminAkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Akun Keuangan"),
        ];
    }
}
