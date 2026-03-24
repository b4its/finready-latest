<?php

namespace App\Filament\Resources\Admin\AdminQuestions\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Select::make('idRoom')
                    ->label('Nama Room')
                    ->relationship('room', 'name') // Mengambil relasi 'module' dan menampilkan kolom 'name'
                    ->required()
                    ->searchable()
                    ->preload(),

                RichEditor::make('question')
                    ->label('Pertanyaann')
                    ->columnSpanFull()
                    ->required(),

                TextInput::make('optionA')
                    ->label('Opsi A')
                    ->required(),

                TextInput::make('optionB')
                    ->label('Opsi B')
                    ->required(),

                TextInput::make('optionC')
                    ->label('Opsi C')
                    ->required(),

                TextInput::make('optionD')
                    ->label('Opsi D')
                    ->required(),

                Select::make('key_answer')
                    ->label('Kunci Jawaban')
                    ->options([
                        'a' => 'A',
                        'b' => 'B',
                        'c' => 'C',
                        'd' => 'D',
                    ])
                    ->required(),



    
                
            ]);
    }
}
