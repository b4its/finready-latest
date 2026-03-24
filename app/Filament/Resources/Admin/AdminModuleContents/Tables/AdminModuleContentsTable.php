<?php

namespace App\Filament\Resources\Admin\AdminModuleContents\Tables;

use App\Models\ModuleContent;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class AdminModuleContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                ModuleContent::query()
                    ->selectRaw('module_content.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc') // urutkan tampilannya dari terbaru
            )
            ->columns([
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),

                TextColumn::make('module.name')
                    ->label('Modul Materi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Judul Konten')
                    ->searchable()
                    ->sortable(),
                
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // ACTION 1: View Detail
                ViewAction::make()
                    ->label('View Detail')
                    ->modalHeading('Detail Modul Konten')
                    ->form([
                        Select::make('idModule')
                            ->relationship('module', 'name')
                            ->label('Modul Materi')
                            ->disabled(),

                        TextInput::make('title')
                            ->label('Title')
                            ->disabled(),

                        TextInput::make('type')
                            ->label('Tipe Konten')
                            ->disabled(),

                        TextInput::make('url')
                            ->label('Link Video')
                            // Hanya tampil jika tipe konten adalah video
                            ->visible(fn (Get $get) => $get('type') === 'video')
                            ->disabled(),

                        // Render HTML content menggunakan Placeholder & HtmlString
                        Placeholder::make('content_view')
                            ->label('Content')
                            ->content(function (?Model $record) {
                                if (!$record || empty($record->content)) {
                                    return '-';
                                }
                                // Mem-parsing tag HTML agar dirender dengan benar, bukan sebagai teks biasa
                                return new HtmlString($record->content);
                            })
                            ->columnSpanFull(),

                        // Repeater untuk JSON Document (Otomatis membaca field document_json)
                        Repeater::make('document_json')
                            ->label('List Dokumen')
                            // Sembunyikan jika tipe konten adalah video
                            ->visible(fn (Get $get) => $get('type') !== 'video') 
                            ->schema([
                                TextInput::make('title')
                                    ->label('Nama Dokumen')
                                    ->disabled(),
                                    
                                TextInput::make('dokumen_type')
                                    ->label('Tipe Dokumen')
                                    ->disabled(),
                                    
                                TextInput::make('dokumen_url')
                                ->label('URL / Path Dokumen')
                                ->disabled()
                                ->placeholder('Tidak ada file')
                                ->suffixAction(
                                    fn ($state) => $state ? 
                                        Action::make('open_url')
                                            ->icon('heroicon-m-arrow-top-right-on-square')
                                            ->url(asset($state)) // Menggunakan helper asset() seperti permintaan Anda
                                            ->openUrlInNewTab()
                                        : null
    )
                            ])
                            ->columns(3) // Tampilkan 3 input sejajar agar rapi
                            // Konfigurasi agar Repeater hanya Read-Only
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->collapsible()
                            ->collapsed(false)
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Dokumen'),
                    ]), // <-- TUTUP KURUNG FORM DAN VIEW ACTION DI SINI

                // ACTION 2: Edit
                EditAction::make(),

                // ACTION 3: Delete
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Hapus')
                    ->modalDescription('Apakah yakin ingin menghapus data ini?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->before(function (ModuleContent $record) {
                        // Cek apakah document_json tidak kosong dan merupakan array
                        if (is_array($record->document_json) && count($record->document_json) > 0) {
                            
                            // Lakukan looping pada setiap item dokumen
                            foreach ($record->document_json as $item) {
                                
                                // Pastikan key 'dokumen_url' ada dan tidak kosong
                                if (!empty($item['dokumen_url'])) {
                                    
                                    // Tembak path file-nya
                                    $filePath = public_path($item['dokumen_url']);
                                    
                                    // Cek apakah file fisik benar-benar ada, lalu hapus
                                    if (File::exists($filePath)) {
                                        File::delete($filePath);
                                    }
                                }
                            }
                        }
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([ 
                    \Filament\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                            // 1. Looping untuk setiap baris data (record) yang dicentang di tabel
                            foreach ($records as $record) {
                                
                                // 2. Cek apakah document_json tidak kosong dan merupakan array
                                if (is_array($record->document_json) && count($record->document_json) > 0) {
                                    
                                    // 3. Lakukan looping pada setiap item dokumen di dalam JSON
                                    foreach ($record->document_json as $item) {
                                        
                                        // Pastikan key 'dokumen_url' ada dan tidak kosong
                                        if (!empty($item['dokumen_url'])) {
                                            
                                            // Tembak path file-nya
                                            $filePath = public_path($item['dokumen_url']);
                                            
                                            // Cek apakah file fisik benar-benar ada, lalu hapus
                                            if (\Illuminate\Support\Facades\File::exists($filePath)) {
                                                \Illuminate\Support\Facades\File::delete($filePath);
                                            }
                                        }
                                    }
                                }
                                
                            }
                        }),
                ]),
            ]);
    }
}