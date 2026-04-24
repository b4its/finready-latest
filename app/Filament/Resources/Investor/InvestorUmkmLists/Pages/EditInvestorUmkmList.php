<?php

namespace App\Filament\Resources\Investor\InvestorUmkmLists\Pages;

use App\Filament\Resources\Investor\InvestorUmkmLists\InvestorUmkmListResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvestorUmkmList extends EditRecord
{
    protected static string $resource = InvestorUmkmListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
