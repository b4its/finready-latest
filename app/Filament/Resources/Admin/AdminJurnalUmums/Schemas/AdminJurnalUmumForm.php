<?php

namespace App\Filament\Resources\Admin\AdminJurnalUmums\Schemas;

use App\Models\AkunKeuangan;
use App\Models\DetailAkunKeuangan;
use Filament\Actions\Action; // Pastikan ini benar sesuai namespace Filament v5
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Storage;

class AdminJurnalUmumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            // Pastikan kolom root diset 1
            ->columns(1)
            ->components([
                // ==========================================
                // 1. BAGIAN ATAS: PREVIEW TOTAL JURNAL (REAKTIF DARI REPEATER)
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

                            foreach ($details as $detail) {
                                // PERBAIKAN: Bersihkan string dari titik/koma format ribuan sebelum di-float
                                $rawAmount = $detail['amount'] ?? 0;
                                if (is_string($rawAmount)) {
                                    $amount = (float) str_replace(['.', ','], ['', '.'], $rawAmount);
                                } else {
                                    $amount = (float) $rawAmount;
                                }

                                $akunId = $detail['idAkunKeuangan'] ?? null;
                                $isDebet = $detail['is_debet'] ?? null;
                                
                                if ($amount > 0 && $akunId && $isDebet) {
                                    $akunName = \App\Models\AkunKeuangan::find($akunId)?->name ?? '...';
                                    $formattedAmount = number_format($amount, 0, ',', '.');
                                    
                                    if ($isDebet === 'D') {
                                        $totalDebet += $amount;
                                        $htmlLines .= "<div style='padding-left: 1.5rem; color: #4ade80;'>{$akunName} &middot; Rp {$formattedAmount} (D)</div>";
                                    } else {
                                        $totalKredit += $amount;
                                        $htmlLines .= "<div style='padding-left: 1.5rem; color: #f87171;'>{$akunName} &middot; Rp {$formattedAmount} (K)</div>";
                                    }
                                }
                            }

                            $formattedTotalD = number_format($totalDebet, 0, ',', '.');
                            $formattedTotalK = number_format($totalKredit, 0, ',', '.');
                            
                            // Menggunakan bcsub atau pembulatan untuk menghindari error floating point PHP
                            $isBalanced = (round($totalDebet, 2) === round($totalKredit, 2) && $totalDebet > 0);

                            $balanceStatus = $isBalanced 
                                ? "<span style='color: #4ade80; font-weight: bold;'>[BALANCE]</span>" 
                                : "<span style='color: #f87171; font-weight: bold;'>[TIDAK BALANCE]</span>";

                            return new \Illuminate\Support\HtmlString("
                                <div style='font-family: monospace; font-size: 0.875rem; background-color: #111827; padding: 1.25rem; border-radius: 0.5rem; width: 100%;'>
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
                            ->placeholder('Penjualan barang dagangan kepada...')
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'Tunai' => 'Tunai',
                                'Kredit' => 'Kredit',
                                'Transfer' => 'Transfer Bank',
                            ])
                            ->default('Tunai')
                            ->columnSpanFull(),
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
                                Select::make('is_debet')
                                    ->label('Posisi')
                                    ->options([
                                        "D" => 'Debet',
                                        "K" => 'Kredit',
                                    ])
                                    ->live() 
                                    ->required()
                                    ->columnSpanFull(),

                                Select::make('idAkunKeuangan')
                                    ->relationship('akunKeuangan', 'name')
                                    ->label('Akun')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if (!$state) {
                                            $set('amount', 0);
                                            return;
                                        }
                                        $saldoNormal = DetailAkunKeuangan::where('idAkunKeuangan', $state)->value('nominal') ?? 0;
                                    })
                                    ->allowHtml()
                                    
                                    // --- MAPPING PADA LIST DROPDOWN ---
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        // Mapping Kategori Utama
                                        $categoryMap = [
                                            "aset" => 'Aset',
                                            "pendapatan" => 'Pendapatan',
                                            "beban_biaya" => 'Beban atau Biaya',
                                            "modal" => 'Modal',
                                            "kewajiban" => 'Kewajiban',
                                            "lain-lain" => 'Pendapatan Lain Lain',
                                        ];

                                        // Mapping Detail Kategori
                                        $detailMap = [
                                            'aset'                     => 'Aset Lancar',
                                            'aset_tetap'               => 'Aset Tetap',
                                            'aset_tak_berwujud'        => 'Aset Tak Berwujud',
                                            'kewajiban_jangka_pendek'  => 'Kewajiban Jangka Pendek',
                                            'kewajiban_jangka_panjang' => 'Kewajiban Jangka Panjang',
                                            'modal'                    => 'Modal',
                                            'beban_biaya'              => 'Beban atau Biaya',
                                            'kewajiban'                => 'Kewajiban',
                                            'pendapatan'               => 'Pendapatan',
                                            'lain-lain'                => 'Pendapatan Lain Lain',
                                        ];

                                        $categoryLabel = $categoryMap[$record->category] ?? $record->category;
                                        $detailLabel = $detailMap[$record->detail_category] ?? $record->detail_category;

                                        return "
                                            <div class='flex flex-col'>
                                                <span class='font-semibold'>{$record->name}</span>
                                                <span class='text-xs text-gray-400'>Kategori: {$categoryLabel} &middot; Detail: {$detailLabel}</span>
                                            </div>
                                        ";
                                    })

                                    ->hintAction(
                                        Action::make('lihatDetailAkun')
                                            ->icon('heroicon-m-information-circle')
                                            ->modalHeading('Detail Akun')
                                            ->modalSubmitAction(false)
                                            ->disabled(fn (\Filament\Forms\Components\Select $component) => blank($component->getState()))
                                            ->modalContent(function (\Filament\Forms\Components\Select $component) {
                                                $akunId = $component->getState();
                                                $akun = AkunKeuangan::find($akunId);

                                                if (! $akun) return new HtmlString('<p>Akun belum dipilih atau tidak ditemukan.</p>');

                                                // --- MAPPING PADA MODAL CONTENT ---
                                                $categoryMap = [
                                                    "aset" => 'Aset',
                                                    "pendapatan" => 'Pendapatan',
                                                    "beban_biaya" => 'Beban atau Biaya',
                                                    "modal" => 'Modal',
                                                    "kewajiban" => 'Kewajiban',
                                                    "lain-lain" => 'Pendapatan Lain Lain',
                                                ];

                                                $detailMap = [
                                                    'aset'                     => 'Aset Lancar',
                                                    'aset_tetap'               => 'Aset Tetap',
                                                    'aset_tak_berwujud'        => 'Aset Tak Berwujud',
                                                    'kewajiban_jangka_pendek'  => 'Kewajiban Jangka Pendek',
                                                    'kewajiban_jangka_panjang' => 'Kewajiban Jangka Panjang',
                                                    'modal'                    => 'Modal',
                                                    'beban_biaya'              => 'Beban atau Biaya',
                                                    'kewajiban'                => 'Kewajiban',
                                                    'pendapatan'               => 'Pendapatan',
                                                    'lain-lain'                => 'Pendapatan Lain Lain',
                                                ];

                                                $categoryLabel = $categoryMap[$akun->category] ?? $akun->category;
                                                $detailLabel = $detailMap[$akun->detail_category] ?? $akun->detail_category;

                                                return new HtmlString("
                                                    <div style='display: flex; flex-direction: column; gap: 0.5rem;'>
                                                        <div style='font-size: 1.125rem; font-weight: bold; color: #4ade80;'>{$akun->name}</div>
                                                        <div style='color: #e5e7eb;'><strong>Kategori:</strong> {$categoryLabel}</div>
                                                        <div style='color: #e5e7eb;'><strong>Detail:</strong> {$detailLabel}</div>
                                                    </div>
                                                ");
                                            })
                                    )
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
                                            $state = $cleaned . '0';       
                                        }

                                        $set('nominal', (int) $state);
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