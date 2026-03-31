<?php

namespace App\Filament\Resources\Admin\AdminInvestors\Pages;

use App\Filament\Resources\Admin\AdminInvestors\AdminInvestorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminInvestors extends ListRecords
{
    protected static ?string $title = "Daftar Akun Investor";
    protected static string $resource = AdminInvestorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambahkan Akun Investor')
            ->mutateFormDataUsing(function (array $data): array {
                    $data['role'] = "investor";
                    return $data;
                }),
        ];
    }
}
