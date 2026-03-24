<?php

namespace App\Filament\Resources\Admin\AdminModuleContents\Schemas;

use App\Models\Modul;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdminModuleContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('idModule')
                    ->label('Modul Materi')
                    ->options(Modul::all()->pluck('name', 'id'))
                    ->searchable(),

                TextInput::make('title')
                    ->label('Title')
                    ->required(),

                Select::make('type')
                    ->label('Tipe Konten')
                    ->options([
                        'text' => 'Text',
                        'video' => 'Video',
                        'kuis' => 'Kuis',
                    ])
                    ->required()
                    ->live(), 

                TextInput::make('url')
                    ->label('Link Video')
                    ->placeholder('Masukkan link video YouTube')
                    ->visible(fn (Get $get) => $get('type') === 'video')
                    ->required(fn (Get $get) => $get('type') === 'video')
                    ->url(),

                RichEditor::make('content')
                    ->label('Content')
                    ->columnSpanFull(),

                Repeater::make('document_json')
                    ->label('List Dokumen')
                    ->visible(fn (Get $get) => $get('type') !== 'video') 
                    ->addActionLabel('Tambahkan Dokumen')
                    ->minItems(1)
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Dokumen*')
                            ->placeholder('Masukkan Nama Dokumen...')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        // PERHATIAN: Nama field di sini adalah 'dokumen_type'
                        Select::make('dokumen_type')
                            ->label('Tipe Dokumen')
                            ->options([
                                'pdf' => 'Dokumen Pdf',
                                'gambar' => 'Dokumen Gambar',
                            ])
                            ->required()
                            ->live(), 

                        FileUpload::make('dokumen_url')
                            ->label('Upload Dokumen')
                            ->disk('public_folder')
                            ->directory(function (Get $get, ?Model $record) {
                                $repeaterType = $get('dokumen_type') ?? 'temp'; 
                                $id = $record?->id ?? 'temp';
                                return "media/dokumen/{$repeaterType}/{$id}";
                            })
                            ->getUploadedFileNameForStorageUsing(function ($file, ?Model $record) {
                                $ext = $file->getClientOriginalExtension();
                                $datetime = now()->format('Ymd_His');
                                $id = $record?->id ?? 'new';
                                
                                // --- PERBAIKAN: Tambahkan UniqID agar file di repeater tidak saling timpa ---
                                $randomStr = uniqid(); 
                                
                                return "{$datetime}_{$id}_{$randomStr}.{$ext}";
                            })
                            ->visibility('public')
                            ->columnSpanFull()
                            ->preserveFilenames(false)
                            ->deleteUploadedFileUsing(fn (string $file) => Storage::disk('public_folder')->delete($file))
                            ->required(),
                    ])
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Dokumen Baru') 
                    ->collapsible()
                    ->columnSpanFull()
                    ->defaultItems(1),
            ]);
    }
}