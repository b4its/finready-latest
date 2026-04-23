<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwalAslis;

use App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\Pages\CreateUmkmSaldoAwalAsli;
use App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\Pages\EditUmkmSaldoAwalAsli;
use App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\Pages\ListUmkmSaldoAwalAslis;
use App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\Schemas\UmkmSaldoAwalAsliForm;
use App\Filament\Resources\Umkm\UmkmSaldoAwalAslis\Tables\UmkmSaldoAwalAslisTable;
use App\Models\SaldoAwal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmSaldoAwalAsliResource extends Resource
{
    protected static ?string $model = SaldoAwal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'saldo_awal';
    protected static ?string $slug = 'saldo-awal-riil';

    public static function form(Schema $schema): Schema
    {
        return UmkmSaldoAwalAsliForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmSaldoAwalAslisTable::configure($table);
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
        return 'Saldo Awal';
    }

    public static function getNavigationIcon(): string { return 'heroicon-s-banknotes'; }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmSaldoAwalAslis::route('/'),
            // 'create' => CreateUmkmSaldoAwalAsli::route('/create'),
            // 'edit' => EditUmkmSaldoAwalAsli::route('/{record}/edit'),
        ];
    }
}
