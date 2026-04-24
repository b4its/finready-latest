<?php

namespace App\Filament\Resources\Investor\InvestorUmkmLists;

use App\Filament\Resources\Investor\InvestorUmkmLists\Pages\CreateInvestorUmkmList;
use App\Filament\Resources\Investor\InvestorUmkmLists\Pages\EditInvestorUmkmList;
use App\Filament\Resources\Investor\InvestorUmkmLists\Pages\ListInvestorUmkmLists;
use App\Filament\Resources\Investor\InvestorUmkmLists\Schemas\InvestorUmkmListForm;
use App\Filament\Resources\Investor\InvestorUmkmLists\Tables\InvestorUmkmListsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InvestorUmkmListResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'users';
    protected static ?string $slug = 'daftar-umkm';

    public static function form(Schema $schema): Schema
    {
        return InvestorUmkmListForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvestorUmkmListsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function getNavigationLabel(): string
    {
        return 'UMKM';
    }

    public static function getNavigationIcon(): string { return 'heroicon-s-building-office-2'; }


    public static function getPages(): array
    {
        return [
            'index' => ListInvestorUmkmLists::route('/'),
            // 'create' => CreateInvestorUmkmList::route('/create'),
            // 'edit' => EditInvestorUmkmList::route('/{record}/edit'),
        ];
    }
}
