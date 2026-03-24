<?php

namespace App\Filament\Resources\Admin\AdminQuestions\Pages;

use App\Filament\Resources\Admin\AdminQuestions\AdminQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminQuestions extends ListRecords
{
    protected static ?string $title = "Daftar Pertanyaan";
    protected static string $resource = AdminQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambahkan Pertanyaan"),
        ];
    }
}
