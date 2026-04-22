<?php

namespace App\Filament\Resources\Admin\AdminSifatSaldoAkuns\Pages;

use App\Filament\Resources\Admin\AdminSifatSaldoAkuns\AdminSifatSaldoAkunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminSifatSaldoAkuns extends ListRecords
{
    protected static ?string $title = "Daftar Sifat Saldo Akun";
    protected static string $resource = AdminSifatSaldoAkunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Sifat Saldo Akun"),
        ];
    }
}
