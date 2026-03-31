<?php

namespace App\Filament\Resources\Umkm\UmkmPoins\Tables;

use App\Models\LearnProgress;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UmkmPoinsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                LearnProgress::query()
                    ->selectRaw('learn_progress.*, ROW_NUMBER() OVER (ORDER BY created_at desc) as row_num')
                    ->where('idUsers', Auth::user()->id)
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

                TextColumn::make('point')
                    ->label('Poin')
                    ->sortable(),
                

                TextColumn::make('title')
                    ->label('Judul')
                    ->sortable(),            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
