<?php

namespace App\Filament\Resources\Umkm\UmkmPoins\Pages;

use App\Filament\Resources\Umkm\UmkmPoins\UmkmPoinResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmPoins extends ListRecords
{
    protected static ?string $title = "Daftar Poin Diperoleh";
    protected static string $resource = UmkmPoinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
