<?php

namespace App\Filament\Resources\Umkm\UmkmJurnalUmums\Schemas;

use App\Models\AkunKeuangan;
use Filament\Actions\Action;
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

class UmkmJurnalUmumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Preview Jurnal Entry')
                    ->schema([
                        Placeholder::make('preview_jurnal')
                            ->hiddenLabel()
                            ->content(function (Get $get) {
                                $amount = (float) ($get('amount') ?: 0);
                                $formattedAmount = number_format($amount, 0, ',', '.');

                                $akunId = $get('idAkunKeuangan');
                                $akunName = '...';
                                if ($akunId) {
                                    $akun = AkunKeuangan::find($akunId);
                                    $akunName = $akun ? $akun->name : '...';
                                }

                                // Ini menggunakan field is_debet yang baru kamu buat
                                $isDebet = $get('is_debet');
                                
                                if ($isDebet === 'D') {
                                    $debetAkun = $akunName;
                                    $kreditAkun = 'Lawan Transaksi';
                                } elseif ($isDebet === 'K') {
                                    $debetAkun = 'Lawan Transaksi';
                                    $kreditAkun = $akunName;
                                } else {
                                    $debetAkun = '...';
                                    $kreditAkun = '...';
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
                                    ->live() 
                                    ->required(),

                                // PERUBAHAN ADA DI SINI =====================
                                Select::make('idAkunKeuangan')
                                    ->relationship('akunKeuangan', 'name')
                                    ->label('Akun')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->allowHtml()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "
                                        <div class='flex flex-col'>
                                            <span class='font-semibold'>{$record->name}</span>
                                            <span class='text-xs text-gray-400'>Kategori: {$record->category} &middot; Detail: {$record->detail_category}</span>
                                        </div>
                                    ")
                                    // Mengganti helperText menjadi action modal
                                    ->hintAction(
                                        Action::make('lihatDetailAkun')
                                            ->icon('heroicon-m-information-circle')
                                            ->label('Lihat Detail Akun')
                                            ->modalHeading('Detail Akun Keuangan')
                                            ->modalSubmitAction(false) // Sembunyikan tombol submit karena cuma mode baca
                                            ->modalCancelActionLabel('Tutup')
                                            ->disabled(fn (Get $get) => blank($get('idAkunKeuangan'))) // Tombol mati kalau belum pilih akun
                                            ->modalContent(function (Get $get) {
                                                $akunId = $get('idAkunKeuangan');
                                                $akun = AkunKeuangan::find($akunId);

                                                if (! $akun) {
                                                    return new HtmlString('<p>Akun tidak ditemukan.</p>');
                                                }

                                                return new HtmlString("
                                                    <div style='display: flex; flex-direction: column; gap: 1rem;'>
                                                        <div>
                                                            <span style='color: gray; font-size: 0.875rem;'>Nama Akun:</span> <br> 
                                                            <strong style='font-size: 1.125rem;'>{$akun->name}</strong>
                                                        </div>
                                                        <div>
                                                            <span style='color: gray; font-size: 0.875rem;'>Kategori Utama:</span> <br> 
                                                            <strong>{$akun->category}</strong>
                                                        </div>
                                                        <div>
                                                            <span style='color: gray; font-size: 0.875rem;'>Detail Kategori:</span> <br> 
                                                            <strong>{$akun->detail_category}</strong>
                                                        </div>
                                                    </div>
                                                ");
                                            })
                                    )
                                    ->placeholder('Pilih Akun')
                                    ->required(),

                                TextInput::make('amount')
                                    ->label('Jumlah (Rp)')
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->stripCharacters('.')
                                    ->numeric()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->required(),
                                // ===========================================  
                            ]), 

                        Grid::make(2)
                            ->schema([

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
