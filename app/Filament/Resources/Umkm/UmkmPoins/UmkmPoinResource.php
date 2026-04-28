<?php

namespace App\Filament\Resources\Umkm\UmkmPoins;

use App\Filament\Resources\Umkm\UmkmPoins\Pages\CreateUmkmPoin;
use App\Filament\Resources\Umkm\UmkmPoins\Pages\EditUmkmPoin;
use App\Filament\Resources\Umkm\UmkmPoins\Pages\ListUmkmPoins;
use App\Filament\Resources\Umkm\UmkmPoins\Schemas\UmkmPoinForm;
use App\Filament\Resources\Umkm\UmkmPoins\Tables\UmkmPoinsTable;
use App\Models\Poin;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmPoinResource extends Resource
{
    protected static ?string $model = Poin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'poin';
    protected static ?string $slug = 'poin';

    public static function form(Schema $schema): Schema
    {
        return UmkmPoinForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmPoinsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Poin';
    }
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-star';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmPoins::route('/'),
            // 'create' => CreateUmkmPoin::route('/create'),
            // 'edit' => EditUmkmPoin::route('/{record}/edit'),
        ];
    }
}
