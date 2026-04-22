<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Pages;

use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\UmkmSifatAkunKeuanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmSifatAkunKeuangans extends ListRecords
{
    protected static string $resource = UmkmSifatAkunKeuanganResource::class;
    protected static ?string $title = 'Daftar Sifat Akun  Keuangan';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahakan Sifat Akun Keuangan"),
        ];
    }
}
