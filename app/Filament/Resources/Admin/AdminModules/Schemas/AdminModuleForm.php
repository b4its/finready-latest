<?php

namespace App\Filament\Resources\Admin\AdminModules\Schemas;

use Filament\Forms\Components\Textarea;
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
                
                    
                TextInput::make('max_point')
                    ->label('Poin Maksimal')
                    ->numeric()
                    ->required(),
                    
                Textarea::make('description')
                        ->label('Deskripsi Modul')
                        ->maxLength(255)
                        ->rows(3)
                        ->columnSpanFull()
                        ->required(),
            ]);
    }
}
