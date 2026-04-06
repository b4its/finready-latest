<?php

namespace App\Filament\Resources\Admin\AdminPoins;

use App\Filament\Resources\Admin\AdminPoins\Pages\CreateAdminPoin;
use App\Filament\Resources\Admin\AdminPoins\Pages\EditAdminPoin;
use App\Filament\Resources\Admin\AdminPoins\Pages\ListAdminPoins;
use App\Filament\Resources\Admin\AdminPoins\Schemas\AdminPoinForm;
use App\Filament\Resources\Admin\AdminPoins\Tables\AdminPoinsTable;
use App\Models\LearnProgress;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminPoinResource extends Resource
{
    protected static ?string $model = LearnProgress::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'learn_progress';
    protected static ?string $slug = 'learn-progress';

    public static function form(Schema $schema): Schema
    {
        return AdminPoinForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminPoinsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Poin';
    }
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-star';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminPoins::route('/'),
            'create' => CreateAdminPoin::route('/create'),
            'edit' => EditAdminPoin::route('/{record}/edit'),
        ];
    }
}
