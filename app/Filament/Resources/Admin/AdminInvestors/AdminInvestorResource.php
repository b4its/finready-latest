<?php

namespace App\Filament\Resources\Admin\AdminInvestors;

use App\Filament\Resources\Admin\AdminInvestors\Pages\CreateAdminInvestor;
use App\Filament\Resources\Admin\AdminInvestors\Pages\EditAdminInvestor;
use App\Filament\Resources\Admin\AdminInvestors\Pages\ListAdminInvestors;
use App\Filament\Resources\Admin\AdminInvestors\Schemas\AdminInvestorForm;
use App\Filament\Resources\Admin\AdminInvestors\Tables\AdminInvestorsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminInvestorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'users';
    protected static ?string $title = 'Daftar Akun Investor';
    protected static ?string $slug = 'akun-investor';

    public static function form(Schema $schema): Schema
    {
        return AdminInvestorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminInvestorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Akun';
    }
    public static function getNavigationLabel(): string
    {
        return 'Investor';
    }

    public static function getNavigationIcon(): string
    {
    return 'heroicon-o-user';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminInvestors::route('/'),
            // 'create' => CreateAdminInvestor::route('/create'),
            // 'edit' => EditAdminInvestor::route('/{record}/edit'),
        ];
    }
}
