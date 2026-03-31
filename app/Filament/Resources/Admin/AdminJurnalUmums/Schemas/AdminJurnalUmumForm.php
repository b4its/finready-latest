<?php

namespace App\Filament\Resources\Admin\AdminJurnalUmums\Schemas;

use App\Models\AkunKeuangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
            ->components([
                //
                // ==========================================
                // 1. BAGIAN ATAS: PREVIEW JURNAL (REAKTIF)
                // ==========================================
                Section::make('Preview Jurnal Entry')
                    ->schema([
                        Placeholder::make('preview_jurnal')
                            ->hiddenLabel()
                            ->content(function (Get $get) {
                                // 1. Ambil state nominal dan format ke Rupiah
                                $amount = (float) ($get('amount') ?: 0);
                                $formattedAmount = number_format($amount, 0, ',', '.');

                                // 2. Ambil state nama akun yang dipilih
                                $akunId = $get('idAkunKeuangan');
                                $akunName = '...';
                                if ($akunId) {
                                    $akun = AkunKeuangan::find($akunId);
                                    $akunName = $akun ? $akun->name : '...';
                                }

                                // 3. Ambil state kategori untuk menentukan posisi Debet/Kredit secara dinamis
                                $kategori = $get('kategori');
                                $debetAkun = 'Kas / Bank';
                                $kreditAkun = '...';

                                // Logika sederhana untuk preview posisi jurnal (bisa disesuaikan dengan aturan akutansi sistemmu)
                                if ($kategori === 'pendapatan') {
                                    $debetAkun = 'Kas / Piutang';
                                    $kreditAkun = $akunName !== '...' ? $akunName : 'Pendapatan';
                                } elseif ($kategori === 'pengeluaran' || $kategori === 'aset') {
                                    $debetAkun = $akunName !== '...' ? $akunName : 'Beban / Aset';
                                    $kreditAkun = 'Kas / Utang';
                                } else {
                                    $debetAkun = $akunName;
                                    $kreditAkun = 'Lawan Transaksi';
                                }

                                return new HtmlString("
                                    <div style='font-family: monospace; font-size: 0.875rem; background-color: #111827; padding: 1.25rem; border-radius: 0.5rem; width: 100%;'>
                                        <div style='color: #4ade80; font-weight: bold;'>Debet:</div>
                                        <div style='padding-left: 1.5rem; color: #e5e7eb; margin-bottom: 0.5rem;'>{$debetAkun} &middot; Rp {$formattedAmount}</div>
                                        
                                        <div style='color: #f87171; font-weight: bold;'>Kredit:</div>
                                        <div style='padding-left: 1.5rem; color: #e5e7eb;'>{$kreditAkun} &middot; Rp {$formattedAmount}</div>
                                    </div>
                                ");
                            }),
                    ]),

                // ==========================================
                // 2. BAGIAN BAWAH: INPUT DATA
                // ==========================================
                Section::make('Input Data Transaksi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('periode')
                                    ->label('Tanggal Transaksi')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->required(),

                                TextInput::make('no_referensi')
                                    ->label('No. Referensi')
                                    ->placeholder('JU-2025-XXXX')
                                    ->maxLength(255),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('is_debet')
                                    ->label('Debet atau Kredit')
                                    ->options([
                                        "D" => 'Debet',
                                        "K" => 'Kredit',
                                    ])
                                    ->live() // Memicu re-render pada Preview saat diubah
                                    ->required(),

                        Select::make('idAkunKeuangan')
                            ->relationship('akunKeuangan', 'name') // 'name' tetap sebagai fallback
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->kategori}")
                            ->label('Akun')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->placeholder('Pilih Akun')
                            ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Jumlah (Rp)')
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true) // Update Preview saat pengguna selesai mengetik (kehilangan fokus dari input)
                                    ->required(),

                                Select::make('metode_pembayaran')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        'Tunai' => 'Tunai',
                                        'Kredit' => 'Kredit',
                                        'Transfer' => 'Transfer Bank',
                                    ])
                                    ->default('Tunai'),
                            ]),

                        TextInput::make('no_faktur')
                            ->label('Deskripsi / No. Faktur')
                            ->placeholder('Penjualan barang dagangan kepada...')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Catatan Tambahan')
                            ->placeholder('Opsional — keterangan tambahan...')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('lampiran')
                            ->label('Lampiran Dokumen (Opsional)')
                            ->disk('public_folder')
                            ->directory(function (Get $get, ?Model $record) {
                                $repeaterType = $get('dokumen_type') ?? 'temp';
                                $id = $record?->id ?? 'temp';
                                return "media/dokumen/jurnal_umum/{$repeaterType}/{$id}";
                            })
                            ->getUploadedFileNameForStorageUsing(function ($file, ?Model $record) {
                                $ext = $file->getClientOriginalExtension();
                                $datetime = now()->format('Ymd_His');
                                $id = $record?->id ?? 'new';
                                $randomStr = uniqid();
                                return "{$datetime}_{$id}_{$randomStr}.{$ext}";
                            })
                            ->visibility('public')
                            ->deleteUploadedFileUsing(fn (string $file) => Storage::disk('public_folder')->delete($file))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
