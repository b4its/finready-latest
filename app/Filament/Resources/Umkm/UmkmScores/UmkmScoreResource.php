<?php

namespace App\Filament\Resources\Umkm\UmkmScores;

use App\Filament\Resources\Umkm\UmkmScores\Pages\CreateUmkmScore;
use App\Filament\Resources\Umkm\UmkmScores\Pages\EditUmkmScore;
use App\Filament\Resources\Umkm\UmkmScores\Pages\ListUmkmScores;
use App\Filament\Resources\Umkm\UmkmScores\Schemas\UmkmScoreForm;
use App\Filament\Resources\Umkm\UmkmScores\Tables\UmkmScoresTable;
use App\Models\UmkmScore;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmScoreResource extends Resource
{
    protected static ?string $model = UmkmScore::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'score';

    public static function form(Schema $schema): Schema
    {
        return UmkmScoreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmScoresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmScores::route('/'),
            // 'create' => CreateUmkmScore::route('/create'),
            // 'edit' => EditUmkmScore::route('/{record}/edit'),
        ];
    }
}
