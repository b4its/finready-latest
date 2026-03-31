<?php

namespace App\Filament\Resources\Admin\AdminJurnalUmums;

use App\Filament\Resources\Admin\AdminJurnalUmums\Pages\CreateAdminJurnalUmum;
use App\Filament\Resources\Admin\AdminJurnalUmums\Pages\EditAdminJurnalUmum;
use App\Filament\Resources\Admin\AdminJurnalUmums\Pages\ListAdminJurnalUmums;
use App\Filament\Resources\Admin\AdminJurnalUmums\Schemas\AdminJurnalUmumForm;
use App\Filament\Resources\Admin\AdminJurnalUmums\Tables\AdminJurnalUmumsTable;
use App\Models\JurnalUmum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminJurnalUmumResource extends Resource
{
    protected static ?string $model = JurnalUmum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'jurnal_umum';
    protected static ?string $title = 'Daftar Jurnal Umum';
    protected static ?string $slug = 'jurnal-umum';

    public static function form(Schema $schema): Schema
    {
        return AdminJurnalUmumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminJurnalUmumsTable::configure($table);
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
        return 'Jurnal Umum';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminJurnalUmums::route('/'),
            // 'create' => CreateAdminJurnalUmum::route('/create'),
            // 'edit' => EditAdminJurnalUmum::route('/{record}/edit'),
        ];
    }
}
