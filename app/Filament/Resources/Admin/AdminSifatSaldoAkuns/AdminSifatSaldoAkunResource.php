<?php

namespace App\Filament\Resources\Admin\AdminSifatSaldoAkuns;

use App\Filament\Resources\Admin\AdminSifatSaldoAkuns\Pages\CreateAdminSifatSaldoAkun;
use App\Filament\Resources\Admin\AdminSifatSaldoAkuns\Pages\EditAdminSifatSaldoAkun;
use App\Filament\Resources\Admin\AdminSifatSaldoAkuns\Pages\ListAdminSifatSaldoAkuns;
use App\Filament\Resources\Admin\AdminSifatSaldoAkuns\Schemas\AdminSifatSaldoAkunForm;
use App\Filament\Resources\Admin\AdminSifatSaldoAkuns\Tables\AdminSifatSaldoAkunsTable;
use App\Models\AdminSifatSaldoAkun;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminSifatSaldoAkunResource extends Resource
{
    protected static ?string $model = AdminSifatSaldoAkun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'detail_akun_keuangan';

    public static function form(Schema $schema): Schema
    {
        return AdminSifatSaldoAkunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminSifatSaldoAkunsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Keuangan';
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
            'index' => ListAdminSifatSaldoAkuns::route('/'),
            // 'create' => CreateAdminSifatSaldoAkun::route('/create'),
            // 'edit' => EditAdminSifatSaldoAkun::route('/{record}/edit'),
        ];
    }
}
