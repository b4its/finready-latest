<?php

namespace App\Filament\Resources\Umkm\UmkmScores\Pages;

use App\Filament\Resources\Umkm\UmkmScores\UmkmScoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUmkmScores extends ListRecords
{
    protected static string $resource = UmkmScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
