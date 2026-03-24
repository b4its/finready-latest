<?php

namespace App\Filament\Resources\Admin\AdminModuleContents;

use App\Filament\Resources\Admin\AdminModuleContents\Pages\CreateAdminModuleContent;
use App\Filament\Resources\Admin\AdminModuleContents\Pages\EditAdminModuleContent;
use App\Filament\Resources\Admin\AdminModuleContents\Pages\ListAdminModuleContents;
use App\Filament\Resources\Admin\AdminModuleContents\Schemas\AdminModuleContentForm;
use App\Filament\Resources\Admin\AdminModuleContents\Tables\AdminModuleContentsTable;
use App\Models\ModuleContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminModuleContentResource extends Resource
{
    protected static ?string $model = ModuleContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'module_content';
    protected static ?string $slug = 'module-content';

    public static function form(Schema $schema): Schema
    {
        return AdminModuleContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminModuleContentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Modul Konten';
    }
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document-text';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminModuleContents::route('/'),
            // 'create' => CreateAdminModuleContent::route('/create'),
            // 'edit' => EditAdminModuleContent::route('/{record}/edit'),
        ];
    }
}
