<?php

namespace App\Filament\Resources\Admin\AdminQuestions\Pages;

use App\Filament\Resources\Admin\AdminQuestions\AdminQuestionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminQuestion extends EditRecord
{
    protected static string $resource = AdminQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
