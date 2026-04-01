<?php

namespace App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Pages;

use App\Filament\Resources\Admin\AdminSaldoNormalAkuns\AdminSaldoNormalAkunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminSaldoNormalAkuns extends ListRecords
{
    protected static ?string $title = "Daftar Saldo Normal Akun";
    protected static string $resource = AdminSaldoNormalAkunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Saldo Normal Akun"),
        ];
    }
}
