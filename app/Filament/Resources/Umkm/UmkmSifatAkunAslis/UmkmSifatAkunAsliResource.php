<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunAslis;

use App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Pages\CreateUmkmSifatAkunAsli;
use App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Pages\EditUmkmSifatAkunAsli;
use App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Pages\ListUmkmSifatAkunAslis;
use App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Schemas\UmkmSifatAkunAsliForm;
use App\Filament\Resources\Umkm\UmkmSifatAkunAslis\Tables\UmkmSifatAkunAslisTable;
use App\Models\DetailAkunKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmSifatAkunAsliResource extends Resource
{
    protected static ?string $model = DetailAkunKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'detail_akun_keuangan';
    protected static ?string $title = 'sifat-akun-keuangan-riil';

    public static function form(Schema $schema): Schema
    {
        return UmkmSifatAkunAsliForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmSifatAkunAslisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Riil';
    }

    public static function getNavigationLabel(): string
    {
        return 'Sifat Akun Keuangan';
    }

    public static function getNavigationIcon(): string { return 'heroicon-s-shield-check'; }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmSifatAkunAslis::route('/'),
            // 'create' => CreateUmkmSifatAkunAsli::route('/create'),
            // 'edit' => EditUmkmSifatAkunAsli::route('/{record}/edit'),
        ];
    }
}
