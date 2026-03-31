<?php

namespace App\Filament\Resources\Admin\AdminUmkms;

use App\Filament\Resources\Admin\AdminUmkms\Pages\CreateAdminUmkm;
use App\Filament\Resources\Admin\AdminUmkms\Pages\EditAdminUmkm;
use App\Filament\Resources\Admin\AdminUmkms\Pages\ListAdminUmkms;
use App\Filament\Resources\Admin\AdminUmkms\Schemas\AdminUmkmForm;
use App\Filament\Resources\Admin\AdminUmkms\Tables\AdminUmkmsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminUmkmResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'users';
    protected static ?string $title = 'Daftar Akun UMKM';
    protected static ?string $slug = 'akun-umkm';

    public static function form(Schema $schema): Schema
    {
        return AdminUmkmForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminUmkmsTable::configure($table);
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
        return 'UMKM';
    }

    public static function getNavigationIcon(): string
    {
    return 'heroicon-o-user';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminUmkms::route('/'),
            // 'create' => CreateAdminUmkm::route('/create'),
            // 'edit' => EditAdminUmkm::route('/{record}/edit'),
        ];
    }
}
