<?php

namespace App\Filament\Resources\Admin\AdminSaldoAwals;

use App\Filament\Resources\Admin\AdminSaldoAwals\Pages\CreateAdminSaldoAwal;
use App\Filament\Resources\Admin\AdminSaldoAwals\Pages\EditAdminSaldoAwal;
use App\Filament\Resources\Admin\AdminSaldoAwals\Pages\ListAdminSaldoAwals;
use App\Filament\Resources\Admin\AdminSaldoAwals\Schemas\AdminSaldoAwalForm;
use App\Filament\Resources\Admin\AdminSaldoAwals\Tables\AdminSaldoAwalsTable;
use App\Models\SaldoAwal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminSaldoAwalResource extends Resource
{
    protected static ?string $model = SaldoAwal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'saldo_awal';
    protected static ?string $slug = 'saldo-awal';

    public static function form(Schema $schema): Schema
    {
        return AdminSaldoAwalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminSaldoAwalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Laporan Keuangan';
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
            'index' => ListAdminSaldoAwals::route('/'),
            // 'create' => CreateAdminSaldoAwal::route('/create'),
            // 'edit' => EditAdminSaldoAwal::route('/{record}/edit'),
        ];
    }
}
