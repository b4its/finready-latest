<?php

namespace App\Filament\Resources\Admin\AdminModules\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                TextInput::make('name')
                    ->label('Nama Modul')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
