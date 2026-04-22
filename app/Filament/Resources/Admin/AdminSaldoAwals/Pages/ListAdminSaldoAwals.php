<?php

namespace App\Filament\Resources\Admin\AdminSaldoAwals\Pages;

use App\Filament\Resources\Admin\AdminSaldoAwals\AdminSaldoAwalResource;
use App\Models\DetailSaldoAwal;
use App\Models\SaldoAwal;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminSaldoAwals extends ListRecords
{
    protected static ?string $title = "Daftar Saldo Awal";
    protected static string $resource = AdminSaldoAwalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Saldo Awal")
            
        ];
    }

    
}
