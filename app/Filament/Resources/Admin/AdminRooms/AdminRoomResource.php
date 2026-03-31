<?php

namespace App\Filament\Resources\Admin\AdminRooms;

use App\Filament\Resources\Admin\AdminRooms\Pages\CreateAdminRoom;
use App\Filament\Resources\Admin\AdminRooms\Pages\EditAdminRoom;
use App\Filament\Resources\Admin\AdminRooms\Pages\ListAdminRooms;
use App\Filament\Resources\Admin\AdminRooms\Schemas\AdminRoomForm;
use App\Filament\Resources\Admin\AdminRooms\Tables\AdminRoomsTable;
use App\Models\Room;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminRoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'room';
    protected static ?string $title = 'Kuis';
    protected static ?string $slug = 'list-kuis';

    public static function form(Schema $schema): Schema
    {
        return AdminRoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminRoomsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): string
    {
        return 'Kuis';
    }

    public static function getNavigationLabel(): string
    {
        return 'Kuis';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }
    
    public static function getPages(): array
    {
        return [
            'index' => ListAdminRooms::route('/'),
            // 'create' => CreateAdminRoom::route('/create'),
            // 'edit' => EditAdminRoom::route('/{record}/edit'),
        ];
    }
}
