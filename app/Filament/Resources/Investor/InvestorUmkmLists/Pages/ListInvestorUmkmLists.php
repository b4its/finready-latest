<?php

namespace App\Filament\Resources\Investor\InvestorUmkmLists\Pages;

use App\Filament\Resources\Investor\InvestorUmkmLists\InvestorUmkmListResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvestorUmkmLists extends ListRecords
{
    protected static ?string $title = "Daftar UMKM";
    protected static string $resource = InvestorUmkmListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
