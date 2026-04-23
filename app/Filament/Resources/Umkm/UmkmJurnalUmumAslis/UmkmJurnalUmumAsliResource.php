<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmumAslis;

use App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\Pages\CreateUmkmJurnalUmumAsli;
use App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\Pages\EditUmkmJurnalUmumAsli;
use App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\Pages\ListUmkmJurnalUmumAslis;
use App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\Schemas\UmkmJurnalUmumAsliForm;
use App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\Tables\UmkmJurnalUmumAslisTable;
use App\Models\JurnalUmum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmJurnalUmumAsliResource extends Resource
{
    protected static ?string $model = JurnalUmum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jurnal_umum';
    protected static ?string $slug = 'jurnal-umum-riil';

    public static function form(Schema $schema): Schema
    {
        return UmkmJurnalUmumAsliForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmJurnalUmumAslisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Akun Riil';
    }

    public static function getNavigationLabel(): string
    {
        return 'Jurnal Umum Riil';
    }

    public static function getNavigationIcon(): string { return 'heroicon-s-clipboard-document-check'; }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmJurnalUmumAslis::route('/'),
            // 'create' => CreateUmkmJurnalUmumAsli::route('/create'),
            // 'edit' => EditUmkmJurnalUmumAsli::route('/{record}/edit'),
        ];
    }
}
