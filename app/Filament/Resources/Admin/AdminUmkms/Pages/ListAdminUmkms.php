<?php

namespace App\Filament\Resources\Admin\AdminUmkms\Pages;

use App\Filament\Resources\Admin\AdminUmkms\AdminUmkmResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminUmkms extends ListRecords
{
    protected static ?string $title = "Daftar Akun UMKM";
    protected static string $resource = AdminUmkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambahkan Akun UMKM')
            ->mutateFormDataUsing(function (array $data): array {
                    $data['role'] = "umkm";
                    return $data;
                }),
        ];
    }
}
