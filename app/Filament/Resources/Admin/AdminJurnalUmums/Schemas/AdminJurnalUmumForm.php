<?php

namespace App\Filament\Resources\Admin\AdminJurnalUmums\Schemas;

use App\Models\AkunKeuangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;

class AdminJurnalUmumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // ==========================================
                // 1. BAGIAN ATAS: PREVIEW TOTAL JURNAL
                // ==========================================
                Section::make('Preview Jurnal Entry')
                    ->schema([
                        Placeholder::make('preview_jurnal')
                            ->hiddenLabel()
                            ->content(function (Get $get) {
                                $details = $get('details') ?? [];
                                
                                $totalDebet = 0;
                                $totalKredit = 0;
                                $htmlLines = "";

                                // Ambil nama akun dari Header (karena akun ada di header di model Anda)
                                $headerAkunId = $get('idAkunKeuangan');
                                $akunName = 'Akun Header';
                                if ($headerAkunId) {
                                     $akunName = AkunKeuangan::find($headerAkunId)?->name ?? 'Akun Header';
                                }

                                foreach ($details as $index => $detail) {
                                    $rawAmount = $detail['amount'] ?? 0;
                                    
                                    if (is_string($rawAmount)) {
                                        $amount = (float) str_replace(['.', ','], ['', '.'], $rawAmount);
                                    } else {
                                        $amount = (float) $rawAmount;
                                    }

                                    $isDebet = $detail['is_debet'] ?? null;
                                    
                                    if ($amount > 0 && $isDebet) {
                                        $formattedAmount = number_format($amount, 0, ',', '.');
                                        
                                        // Karena model detail tidak punya akun, kita pakai indikator Baris
                                        $barisName = "Baris " . ($index + 1);

                                        if ($isDebet === 'D') {
                                            $totalDebet += $amount;
                                            $htmlLines .= "<div style='padding-left: 1.5rem; color: #4ade80;'>{$barisName} &middot; Rp {$formattedAmount} (D)</div>";
                                        } else {
                                            $totalKredit += $amount;
                                            $htmlLines .= "<div style='padding-left: 1.5rem; color: #f87171;'>{$barisName} &middot; Rp {$formattedAmount} (K)</div>";
                                        }
                                    }
                                }

                                $formattedTotalD = number_format($totalDebet, 0, ',', '.');
                                $formattedTotalK = number_format($totalKredit, 0, ',', '.');
                                
                                $isBalanced = (round($totalDebet, 2) === round($totalKredit, 2) && $totalDebet > 0);

                                $balanceStatus = $isBalanced 
                                    ? "<span style='color: #4ade80; font-weight: bold;'>[BALANCE]</span>" 
                                    : "<span style='color: #f87171; font-weight: bold;'>[TIDAK BALANCE]</span>";

                                return new HtmlString("
                                    <div style='font-family: monospace; font-size: 0.875rem; background-color: #111827; padding: 1.25rem; border-radius: 0.5rem; width: 100%;'>
                                        <div style='color: #e5e7eb; font-weight: bold; margin-bottom: 0.5rem;'>Akun Utama: <span style='color: #60a5fa;'>{$akunName}</span></div>
                                        <div style='color: #e5e7eb; font-weight: bold; margin-bottom: 0.5rem;'>Rincian Entri:</div>
                                        {$htmlLines}
                                        <hr style='border-color: #374151; margin: 1rem 0;' />
                                        <div style='display: flex; justify-content: space-between;'>
                                            <div>
                                                <div style='color: #4ade80;'>Total Debet: Rp {$formattedTotalD}</div>
                                                <div style='color: #f87171;'>Total Kredit: Rp {$formattedTotalK}</div>
                                            </div>
                                            <div>{$balanceStatus}</div>
                                        </div>
                                    </div>
                                ");
                            }),
                    ])
                    ->columnSpanFull(),

                // ==========================================
                // 2. BAGIAN BAWAH: INPUT DATA HEADER
                // ==========================================
                Section::make('Informasi Transaksi')
                    ->schema([
                        DatePicker::make('periode')
                            ->label('Periode Transaksi')
                            ->native(false)
                            ->displayFormat('m/Y')
                            ->closeOnDateSelection()
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->placeholder('Penjualan barang dagangan kepada...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('keterangan_lain')
                            ->label('Keterangan Lain')
                            ->placeholder('Keterangan tambahan...')
                            ->rows(3)
                            ->columnSpanFull(),

                        // Akun Keuangan memang ada di Header (jurnal_umum)
                        Select::make('idAkunKeuangan')
                            ->label('Referensi Akun Keuangan Utama')
                            ->options(function () {
                                return AkunKeuangan::get()
                                    ->mapWithKeys(function ($record) {
                                        $namaAkun = $record->name ?? 'Akun Tidak Ditemukan';
                                        return [$record->id => "{$namaAkun} (ID: {$record->id})"];
                                    });
                            })
                            ->live() // Trigger live agar preview di atas terupdate
                            ->required()
                    ])
                    ->columnSpanFull(),

                // ==========================================
                // 3. REPEATER: INPUT DATA DETAIL
                // ==========================================
                Section::make('Detail Jurnal')
                    ->description('Pastikan total Debet dan Kredit bernilai sama (Balance).')
                    ->schema([
                        Repeater::make('details') 
                            ->relationship()
                            ->schema([
                                TextInput::make('no_faktur')
                                    ->label('No. Faktur')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('keterangan')
                                    ->label('Keterangan')
                                    ->maxLength(255)
                                    ->columnSpanFull(),


                                Select::make('is_debet')
                                    ->label('Posisi')
                                    ->options([
                                        "D" => 'Debet',
                                        "K" => 'Kredit',
                                    ])
                                    ->live() 
                                    ->required()
                                    ->columnSpanFull(),

                                // COMPONENT SELECT AKUN KEUANGAN DIHAPUS DARI SINI
                                // Karena di tabel detail_jurnal_umum dan model DetailJurnalUmum TIDAK ADA idAkunKeuangan

                                // Tambahan: Karena di database Anda ada no_faktur dan metode_pembayaran
                                Select::make('metode_pembayaran')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        "tunai" => 'Tunai',
                                        "kredit" => 'Kredit',
                                        "transfer-tunai" => 'Transfer Tunai',
                                    ])
                                    ->live() 
                                    ->required()
                                    ->columnSpanFull(),
                                TextInput::make('amount')
                                    ->label('Jumlah (Rp)')
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->columnSpanFull()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        if (!$state) return;

                                        if (str_starts_with($state, '0') && strlen($state) > 1) {
                                            $cleaned = ltrim($state, '0'); 
                                            $state = $cleaned === '' ? '0' : $cleaned . '0';       
                                        }

                                        $set('amount', (int) $state); // Perbaikan target set state
                                    })
                                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, ',', '.')),
                            ])
                            ->columns(1) 
                            ->defaultItems(2)
                            ->live()
                            ->addActionLabel('Tambah Baris Jurnal')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // ==========================================
                // 4. KETERANGAN & LAMPIRAN
                // ==========================================
                Section::make('Tambahan')
                    ->schema([
                        FileUpload::make('lampiran')
                            ->label('Lampiran Dokumen')
                            ->disk('public_folder')
                            ->directory('media/dokumen/jurnal_umum')
                            ->visibility('public')
                            ->deleteUploadedFileUsing(fn (string $file) => Storage::disk('public_folder')->delete($file))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}