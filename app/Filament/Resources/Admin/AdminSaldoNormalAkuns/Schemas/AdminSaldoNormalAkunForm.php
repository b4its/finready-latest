<?php

namespace App\Filament\Resources\Admin\AdminSaldoNormalAkuns\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;

class AdminSaldoNormalAkunForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Select::make('idUsers')
                    ->label('Pilih Pengguna')
                    ->relationship(
                        name: 'user', 
                        titleAttribute: 'name',
                        // Filter: Hanya tampilkan user dengan role umkm
                        modifyQueryUsing: fn ($query) => $query->where('role', 'umkm') 
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                Select::make('idAkunKeuangan')
                    ->relationship('akunKeuangan', 'name')
                    ->label('Pilih Akun Keuangan')
                    // Memodifikasi tampilan dropdown agar menampilkan No Referensi & Nama Akun
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->no_referensi} - {$record->name}")
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('nominal')
                    ->label('Jumlah (Rp)')
                    ->prefix('Rp')
                    // Gunakan mask untuk tampilan ribuan yang cantik
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->stripCharacters('.')
                    ->required()
                    ->live(onBlur: true) // Gunakan onBlur agar tidak berat saat mengetik
                    ->columnSpanFull()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        if (!$state) return;

                        // Logika Khusus: Jika input diawali '0' dan diikuti angka lain (misal: 05)
                        // Kita ubah menjadi 50 sesuai request Anda
                        if (str_starts_with($state, '0') && strlen($state) > 1) {
                            $cleaned = ltrim($state, '0'); // Hapus nol di depan
                            $state = $cleaned . '0';       // Tambahkan nol di belakang
                        }

                        // Pastikan state tetap angka bersih sebelum disimpan/diolah
                        $set('nominal', (int) $state);
                    })
            ]);
    }
}
