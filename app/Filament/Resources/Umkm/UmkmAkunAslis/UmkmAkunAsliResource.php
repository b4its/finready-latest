<?php

namespace App\Filament\Resources\Umkm\UmkmAkunAslis;

use App\Filament\Resources\Umkm\UmkmAkunAslis\Pages\CreateUmkmAkunAsli;
use App\Filament\Resources\Umkm\UmkmAkunAslis\Pages\EditUmkmAkunAsli;
use App\Filament\Resources\Umkm\UmkmAkunAslis\Pages\ListUmkmAkunAslis;
use App\Filament\Resources\Umkm\UmkmAkunAslis\Schemas\UmkmAkunAsliForm;
use App\Filament\Resources\Umkm\UmkmAkunAslis\Tables\UmkmAkunAslisTable;
use App\Models\AkunKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmAkunAsliResource extends Resource
{
    protected static ?string $model = AkunKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'akun_keuangan';
    protected static ?string $slug = 'akun-keuangan-riil';

    public static function form(Schema $schema): Schema
    {
        return UmkmAkunAsliForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmAkunAslisTable::configure($table);
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
        return 'Akun Keuangan';
    }

    public static function getNavigationIcon(): string { return 'heroicon-s-building-office-2'; }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmAkunAslis::route('/'),
            // 'create' => CreateUmkmAkunAsli::route('/create'),
            // 'edit' => EditUmkmAkunAsli::route('/{record}/edit'),
        ];
    }
}
