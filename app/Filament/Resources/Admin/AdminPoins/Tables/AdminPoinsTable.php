<?php

namespace App\Filament\Resources\Admin\AdminPoins\Tables;

use App\Models\LearnProgress;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminPoinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                LearnProgress::query()
                    ->selectRaw('learn_progress.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->orderBy('created_at', 'desc') // urutkan tampilannya dari terbaru
            )
            ->columns([
                TextColumn::make('row_num')
                    ->label('No')
                    ->sortable(),
                TextColumn::make('modul.name')
                    ->label('Nama Modul')
                    ->sortable(),

                TextColumn::make('moduleContent.title')
                    ->label('Nama Modul Konten')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Judul')
                    ->sortable(),

                TextColumn::make('point')
                    ->label('Poin')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->emptyStateHeading('Tidak ada Data Poin')
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
