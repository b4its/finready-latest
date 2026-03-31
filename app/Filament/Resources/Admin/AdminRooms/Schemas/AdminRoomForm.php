<?php

namespace App\Filament\Resources\Admin\AdminRooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminRoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Select::make('idModule')
                    ->label('Module')
                    ->relationship('module', 'name') // Mengambil relasi 'module' dan menampilkan kolom 'name'
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('name')
                    ->label('Room Name')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('duration')
                    ->label('Durasi Waktu')
                    ->numeric()
                    ->required()
            ]);
    }
}
