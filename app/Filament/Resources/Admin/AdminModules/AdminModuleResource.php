<?php

namespace App\Filament\Resources\Admin\AdminModules;

use App\Filament\Resources\Admin\AdminModules\Pages\CreateAdminModule;
use App\Filament\Resources\Admin\AdminModules\Pages\EditAdminModule;
use App\Filament\Resources\Admin\AdminModules\Pages\ListAdminModules;
use App\Filament\Resources\Admin\AdminModules\Schemas\AdminModuleForm;
use App\Filament\Resources\Admin\AdminModules\Tables\AdminModulesTable;
use App\Models\AdminModule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminModuleResource extends Resource
{
    protected static ?string $model = AdminModule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'module';
    protected static ?string $slug = 'modul';

    public static function form(Schema $schema): Schema
    {
        return AdminModuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminModulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Modul';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-tag';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminModules::route('/'),
            // 'create' => CreateAdminModule::route('/create'),
            // 'edit' => EditAdminModule::route('/{record}/edit'),
        ];
    }
}
