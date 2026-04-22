<?php

namespace App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\Pages;

use App\Filament\Resources\Umkm\UmkmSifatAkunKeuangans\UmkmSifatAkunKeuanganResource;
use App\Models\PraktekKeuangan;
use App\Models\DetailAkunKeuangan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListUmkmSifatAkunKeuangans extends ListRecords
{
    protected static string $resource = UmkmSifatAkunKeuanganResource::class;
    protected static ?string $title = 'Daftar Sifat Akun Keuangan';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambahkan Sifat Akun Keuangan")
                ->mutateFormDataUsing(function (array $data): array {
                    $data['tipe'] = 2; // Indikator bahwa ini adalah data jawaban praktek
                    return $data;
                })
                ->using(function (array $data, string $model): Model {
                    return DB::transaction(function () use ($data, $model) {
                        
                        // 1. Ambil input dari form. 
                        // Pastikan key ini sesuai dengan nama field di form (contoh: Select::make('idAkunKeuangan'))
                        $idAkunKeuangan = $data['idAkunKeuangan'] ?? $data['akunKeuangan.id'] ?? null;
                        $inputIsDebet = $data['is_debet'];

                        // 2. Tarik kunci jawaban dari database (tipe = 0, idUsers = null)
                        $kunciJawaban = DetailAkunKeuangan::where('idAkunKeuangan', $idAkunKeuangan)
                            ->where('tipe', 0)
                            ->whereNull('idUsers')
                            ->first();

                        // 3. Tentukan status jawaban (1 = Benar, 0 = Salah)
                        $statusAnswer = 0;
                        if ($kunciJawaban && $kunciJawaban->is_debet === $inputIsDebet) {
                            $statusAnswer = 1;
                        }

                        // 4. BUAT RECORD UTAMA (DetailAkunKeuangan)
                        // Ini wajib dilakukan agar Filament menerima return object yang valid
                        $record = $model::create([
                            'idUsers' => Auth::id(),
                            'idAkunKeuangan' => $idAkunKeuangan,
                            'is_debet' => $inputIsDebet,
                            'tipe' => $data['tipe'],
                        ]);

                        // 5. SIMPAN KE LOG PRAKTEK KEUANGAN
                        PraktekKeuangan::create([
                            'idUsers' => Auth::id(),
                            'idAkunKeuangan' => $idAkunKeuangan,    
                            'idJurnalUmum' => null,
                            'table_name' => 'detail_akun_keuangan', // Referensi tabel yang sedang diuji
                            'title' => 'Praktek Sifat Akun Keuangan',
                            'answer' => $inputIsDebet,
                            'status_answer' => $statusAnswer,
                        ]);

                        // Kembalikan model utama agar tabel Filament langsung ter-update
                        return $record; 
                    });
                }),
        ];
    }
}