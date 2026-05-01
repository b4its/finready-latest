<?php

namespace App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans;

use App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\Pages\CreateUmkmPengajuanDataKeuangan;
use App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\Pages\EditUmkmPengajuanDataKeuangan;
use App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\Pages\ListUmkmPengajuanDataKeuangans;
use App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\Schemas\UmkmPengajuanDataKeuanganForm;
use App\Filament\Resources\Umkm\UmkmPengajuanDataKeuangans\Tables\UmkmPengajuanDataKeuangansTable;
use App\Models\PengajuanDataKeuangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UmkmPengajuanDataKeuanganResource extends Resource
{
    protected static ?string $model = PengajuanDataKeuangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'pengajuan_data_keuangan';
    protected static ?string $title = 'Pengajuan Data Keuangan';
    protected static ?string $slug = 'pengajuan-data-keuangan';


    public static function form(Schema $schema): Schema
    {
        return UmkmPengajuanDataKeuanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmPengajuanDataKeuangansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Pengajuan Data Keuangan';
    }
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-clipboard-document-check';
    }


    public static function getPages(): array
    {
        return [
            'index' => ListUmkmPengajuanDataKeuangans::route('/'),
            // 'create' => CreateUmkmPengajuanDataKeuangan::route('/create'),
            // 'edit' => EditUmkmPengajuanDataKeuangan::route('/{record}/edit'),
        ];
    }
}
