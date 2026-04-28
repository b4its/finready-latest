<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmums\Pages;

use App\Filament\Resources\Umkm\UmkmJurnalUmums\UmkmJurnalUmumResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;

class ListUmkmJurnalUmums extends ListRecords
{
    protected static string $resource = UmkmJurnalUmumResource::class;
    protected static ?string $title = "Daftar Jurnal Umum";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambahkan Jurnal Umum")
                ->mutateFormDataUsing(function (array $data): array {
                    $data['idUsers'] = Auth::user()->id;
                    $data['tipe'] = 1;
                    return $data;
                }),

            Action::make('cetak_dokumen')
                ->label('Cetak Dokumen')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->modalHeading('Cetak Laporan Keuangan')
                ->modalDescription('Pilih jenis dokumen dan periode yang ingin Anda cetak.')
                ->modalSubmitActionLabel('Proses Cetak')
                ->form([
                    Select::make('jenis_dokumen')
                        ->label('Jenis Dokumen')
                        ->options([
                            'jurnal_umum' => 'Jurnal Umum',
                            'buku_besar' => 'Buku Besar Umum',
                            'neraca_saldo' => 'Neraca Saldo',
                            'jurnal_penyesuaian' => 'Jurnal Penyesuaian',
                            'laba_rugi' => 'Laba Rugi',
                            'perubahan_modal' => 'Perubahan Modal',
                            'neraca' => 'Neraca',
                            'arus_kas' => 'Arus Kas',
                        ])
                        ->default('buku_besar')
                        ->required(),

                    Select::make('bulan')
                        ->label('Bulan')
                        ->options([
                            '01' => 'Januari',   '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April',     '05' => 'Mei',      '06' => 'Juni',
                            '07' => 'Juli',      '08' => 'Agustus',  '09' => 'September',
                            '10' => 'Oktober',   '11' => 'November', '12' => 'Desember',
                        ])
                        ->default(date('m'))
                        ->required(),

                    TextInput::make('tahun')
                        ->label('Tahun')
                        ->numeric()
                        ->default(date('Y'))
                        ->minValue(2000)
                        ->maxValue(2100)
                        ->required(),
                ])
                ->action(function (array $data) {
                    if ($data['jenis_dokumen'] === 'jurnal_umum') {
                        return redirect()->to(route('jurnal_umum.index', ['bulan' => $data['bulan'], 'tahun' => $data['tahun'], 'idUsers' => Auth::user()->id]));
                    }else if ($data['jenis_dokumen'] === 'buku_besar') {
                        return redirect()->to(route('buku_besar.index', ['bulan' => $data['bulan'], 'tahun' => $data['tahun'], 'idUsers' => Auth::user()->id]));
                    } elseif ($data['jenis_dokumen'] === 'neraca_saldo') {
                        return redirect()->to(route('neraca_saldo.index', ['bulan' => $data['bulan'], 'tahun' => $data['tahun'], 'idUsers' => Auth::user()->id]));
                    } else if ($data['jenis_dokumen'] === 'jurnal_penyesuaian') {
                        // Arahkan ke rute Jurnal Penyesuaian
                        return redirect()->to(route('jurnal_penyesuaian.index', ['bulan' => $data['bulan'], 'tahun' => $data['tahun'], 'idUsers' => Auth::user()->id]));
                    
                    } else if ($data['jenis_dokumen'] === 'laba_rugi') {
                        // Arahkan ke rute Jurnal Penyesuaian
                        return redirect()->to(route('laba_rugi.index', ['bulan' => $data['bulan'], 'tahun' => $data['tahun'], 'idUsers' => Auth::user()->id]));
                    
                    } else if ($data['jenis_dokumen'] === 'perubahan_modal') {
                        // Arahkan ke rute Jurnal Penyesuaian
                        return redirect()->to(route('perubahan_modal.index', ['bulan' => $data['bulan'], 'tahun' => $data['tahun'], 'idUsers' => Auth::user()->id]));
                    
                    } else if ($data['jenis_dokumen'] === 'neraca') {
                        // Arahkan ke rute Jurnal Penyesuaian
                        return redirect()->to(route('neraca.index', ['bulan' => $data['bulan'], 'tahun' => $data['tahun'], 'idUsers' => Auth::user()->id]));
                    
                    } elseif ($data['jenis_dokumen'] === 'arus_kas') {
                        // Arahkan ke rute Jurnal Penyesuaian
                        return redirect()->to(route('arus_kas.index', ['bulan' => $data['bulan'], 'tahun' => $data['tahun'], 'idUsers' => Auth::user()->id]));
                    
                    } 
                }),
        ];
    }
}