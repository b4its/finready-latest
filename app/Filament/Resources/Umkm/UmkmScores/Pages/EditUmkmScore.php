<?php

namespace App\Filament\Resources\Umkm\UmkmScores\Pages;

use App\Filament\Resources\Umkm\UmkmScores\UmkmScoreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUmkmScore extends EditRecord
{
    protected static string $resource = UmkmScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
