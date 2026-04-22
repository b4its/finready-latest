<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans;

use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Pages\CreateUmkmSifatAkunKeuangan;
use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Pages\EditUmkmSifatAkunKeuangan;
use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Pages\ListUmkmSifatAkunKeuangans;
use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Schemas\UmkmSifatAkunKeuanganForm;
use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Tables\UmkmSifatAkunKeuangansTable;
use App\Models\DetailAkunKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmSifatAkunKeuanganResource extends Resource
{
    protected static ?string $model = DetailAkunKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'detail_akun_keuangan';
    protected static ?string $title = 'Sifat Akun Keuangan';
    protected static ?string $slug = 'sifat-akun-keuangan';

    public static function form(Schema $schema): Schema
    {
        return UmkmSifatAkunKeuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmSifatAkunKeuangansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Praktek';
    }

    public static function getNavigationLabel(): string
    {
        return 'Sifat Akun Keuangan';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmSifatAkunKeuangans::route('/'),
            // 'create' => CreateUmkmSifatAkunKeuangan::route('/create'),
            // 'edit' => EditUmkmSifatAkunKeuangan::route('/{record}/edit'),
        ];
    }
}
