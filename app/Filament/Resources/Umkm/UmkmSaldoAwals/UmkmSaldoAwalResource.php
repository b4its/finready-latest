<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwals;

use App\Filament\Resources\Umkm\UmkmSaldoAwals\Pages\CreateUmkmSaldoAwal;
use App\Filament\Resources\Umkm\UmkmSaldoAwals\Pages\EditUmkmSaldoAwal;
use App\Filament\Resources\Umkm\UmkmSaldoAwals\Pages\ListUmkmSaldoAwals;
use App\Filament\Resources\Umkm\UmkmSaldoAwals\Schemas\UmkmSaldoAwalForm;
use App\Filament\Resources\Umkm\UmkmSaldoAwals\Tables\UmkmSaldoAwalsTable;
use App\Models\SaldoAwal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmSaldoAwalResource extends Resource
{
    protected static ?string $model = SaldoAwal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'saldo_awal';
    protected static ?string $slug = 'saldo-awal';

    public static function form(Schema $schema): Schema
    {
        return UmkmSaldoAwalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmSaldoAwalsTable::configure($table);
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
        return 'Saldo Awal';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmSaldoAwals::route('/'),
            // 'create' => CreateUmkmSaldoAwal::route('/create'),
            // 'edit' => EditUmkmSaldoAwal::route('/{record}/edit'),
        ];
    }
}
