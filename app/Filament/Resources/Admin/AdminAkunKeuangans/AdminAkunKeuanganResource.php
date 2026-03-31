<?php

namespace App\Filament\Resources\Admin\AdminAkunKeuangans;

use App\Filament\Resources\Admin\AdminAkunKeuangans\Pages\CreateAdminAkunKeuangan;
use App\Filament\Resources\Admin\AdminAkunKeuangans\Pages\EditAdminAkunKeuangan;
use App\Filament\Resources\Admin\AdminAkunKeuangans\Pages\ListAdminAkunKeuangans;
use App\Filament\Resources\Admin\AdminAkunKeuangans\Schemas\AdminAkunKeuanganForm;
use App\Filament\Resources\Admin\AdminAkunKeuangans\Tables\AdminAkunKeuangansTable;
use App\Models\AkunKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminAkunKeuanganResource extends Resource
{
    protected static ?string $model = AkunKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'akun_keuangan';
    protected static ?string $title = 'Daftar Akun Keuangan';
    protected static ?string $slug = 'akun-keuangan';

    public static function form(Schema $schema): Schema
    {
        return AdminAkunKeuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminAkunKeuangansTable::configure($table);
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
        return 'Akun Keuangan';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminAkunKeuangans::route('/'),
            // 'create' => CreateAdminAkunKeuangan::route('/create'),
            // 'edit' => EditAdminAkunKeuangan::route('/{record}/edit'),
        ];
    }
}
