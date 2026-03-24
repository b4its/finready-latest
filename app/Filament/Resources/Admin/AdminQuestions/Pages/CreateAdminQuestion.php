<?php

namespace App\Filament\Resources\Admin\AdminQuestions\Pages;

use App\Filament\Resources\Admin\AdminQuestions\AdminQuestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminQuestion extends CreateRecord
{
    protected static string $resource = AdminQuestionResource::class;
}
