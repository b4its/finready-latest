<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmums;

use App\Filament\Resources\Umkm\UmkmJurnalUmums\Pages\CreateUmkmJurnalUmum;
use App\Filament\Resources\Umkm\UmkmJurnalUmums\Pages\EditUmkmJurnalUmum;
use App\Filament\Resources\Umkm\UmkmJurnalUmums\Pages\ListUmkmJurnalUmums;
use App\Filament\Resources\Umkm\UmkmJurnalUmums\Schemas\UmkmJurnalUmumForm;
use App\Filament\Resources\Umkm\UmkmJurnalUmums\Tables\UmkmJurnalUmumsTable;
use App\Models\JurnalUmum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmJurnalUmumResource extends Resource
{
    protected static ?string $model = JurnalUmum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jurnal_umum';
    protected static ?string $slug = 'jurnal-umum';

    public static function form(Schema $schema): Schema
    {
        return UmkmJurnalUmumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmJurnalUmumsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Uji Coba';
    }

    public static function getNavigationLabel(): string
    {
        return 'Jurnal Umum';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmJurnalUmums::route('/'),
            // 'create' => CreateUmkmJurnalUmum::route('/create'),
            // 'edit' => EditUmkmJurnalUmum::route('/{record}/edit'),
        ];
    }
}
