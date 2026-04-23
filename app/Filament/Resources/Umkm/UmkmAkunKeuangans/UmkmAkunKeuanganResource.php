<?php

namespace App\Filament\Resources\Umkm\UmkmAkunKeuangans;

use App\Filament\Resources\Umkm\UmkmAkunKeuangans\Pages\CreateUmkmAkunKeuangan;
use App\Filament\Resources\Umkm\UmkmAkunKeuangans\Pages\EditUmkmAkunKeuangan;
use App\Filament\Resources\Umkm\UmkmAkunKeuangans\Pages\ListUmkmAkunKeuangans;
use App\Filament\Resources\Umkm\UmkmAkunKeuangans\Schemas\UmkmAkunKeuanganForm;
use App\Filament\Resources\Umkm\UmkmAkunKeuangans\Tables\UmkmAkunKeuangansTable;
use App\Models\AkunKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmAkunKeuanganResource extends Resource
{
    protected static ?string $model = AkunKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'akun_keuangan';
    
    protected static ?string $slug = 'akun-keuangan';

    public static function form(Schema $schema): Schema
    {
        return UmkmAkunKeuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmAkunKeuangansTable::configure($table);
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
        return 'Akun Keuangan';
    }

public static function getNavigationIcon(): string { return 'heroicon-o-building-library'; }
    


    public static function getPages(): array
    {
        return [
            'index' => ListUmkmAkunKeuangans::route('/'),
            // 'create' => CreateUmkmAkunKeuangan::route('/create'),
            // 'edit' => EditUmkmAkunKeuangan::route('/{record}/edit'),
        ];
    }
}
