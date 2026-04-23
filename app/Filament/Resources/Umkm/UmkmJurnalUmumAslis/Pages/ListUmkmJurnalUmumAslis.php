<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\Pages;

use App\Filament\Resources\Umkm\UmkmJurnalUmumAslis\UmkmJurnalUmumAsliResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUmkmJurnalUmumAslis extends ListRecords
{
    protected static string $resource = UmkmJurnalUmumAsliResource::class;

    protected static ?string $title = "Daftar Jurnal Umum Riil";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambahkan Jurnal Umum Riil")
                ->mutateFormDataUsing(function (array $data): array {
                    $data['idUsers'] = Auth::user()->id;
                    $data['tipe'] = 2;
                    return $data;
                }),
        ];
    }
}
