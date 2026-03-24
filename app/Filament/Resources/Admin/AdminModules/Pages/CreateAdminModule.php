<?php

namespace App\Filament\Resources\Admin\AdminModules\Pages;

use App\Filament\Resources\Admin\AdminModules\AdminModuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminModule extends CreateRecord
{
    protected static string $resource = AdminModuleResource::class;
}
