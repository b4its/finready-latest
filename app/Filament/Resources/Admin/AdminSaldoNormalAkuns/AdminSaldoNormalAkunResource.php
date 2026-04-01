<?php

namespace App\Filament\Resources\Admin\AdminSaldoNormalAkuns;

use App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Pages\CreateAdminSaldoNormalAkun;
use App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Pages\EditAdminSaldoNormalAkun;
use App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Pages\ListAdminSaldoNormalAkuns;
use App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Schemas\AdminSaldoNormalAkunForm;
use App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Tables\AdminSaldoNormalAkunsTable;
use App\Models\DetailAkunKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminSaldoNormalAkunResource extends Resource
{
    protected static ?string $model = DetailAkunKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'detail_akun_keuangan';
        protected static ?string $title = 'Saldo Normal Akun';
    protected static ?string $slug = 'saldo-normal-akun';

    public static function form(Schema $schema): Schema
    {
        return AdminSaldoNormalAkunForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminSaldoNormalAkunsTable::configure($table);
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
        return 'Saldo Normal Akun';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminSaldoNormalAkuns::route('/'),
            // 'create' => CreateAdminSaldoNormalAkun::route('/create'),
            // 'edit' => EditAdminSaldoNormalAkun::route('/{record}/edit'),
        ];
    }
}
