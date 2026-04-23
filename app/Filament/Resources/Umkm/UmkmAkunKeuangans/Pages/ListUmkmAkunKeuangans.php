<?php

namespace App\Filament\Resources\Umkm\UmkmAkunKeuangans\Pages;

use App\Filament\Resources\Umkm\UmkmAkunKeuangans\UmkmAkunKeuanganResource;
use App\Models\PraktekKeuangan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListUmkmAkunKeuangans extends ListRecords
{
    protected static ?string $title = "Daftar Akun Keuangan";
    protected static string $resource = UmkmAkunKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambahkan Akun Keuangan")
                ->mutateFormDataUsing(function (array $data): array {
                    $data['tipe'] = 1; // Indikator data praktek
                    return $data;
                })
                ->using(function (array $data, string $model): Model {
                    return DB::transaction(function () use ($data, $model) {
                        
                        // 1. Buat record Akun Keuangan
                        $record = $model::create($data);

                        // 2. Ambil value dari form
                        $category = $data['category'] ?? '';
                        $detailCategory = $data['detail_category'] ?? '';

                        /**
                         * 3. Mapping Aturan Berdasarkan Key di Schema Form Anda
                         * Kiri: key dari select 'category'
                         * Kanan: daftar key yang valid dari select 'detail_category'
                         */
                        $rules = [
                            'aset' => [
                                'aset',            // Aset Lancar
                                'aset_tetap',      // Aset Tetap
                                'aset_tak_berwujud'// Aset Tak Berwujud
                            ],
                            'kewajiban' => [
                                'kewajiban_jangka_pendek', 
                                'kewajiban_jangka_panjang'
                            ],
                            'modal' => [
                                'modal'
                            ],
                            'pendapatan' => [
                                'pendapatan'
                            ],
                            'beban_biaya' => [
                                'beban_biaya'
                            ],
                            'lain-lain' => [
                                'lain-lain' // Pendapatan Lain Lain
                            ],
                        ];

                        // 4. Validasi kecocokan
                        $statusAnswer = 0; // Default salah
                        if (isset($rules[$category]) && in_array($detailCategory, $rules[$category])) {
                            $statusAnswer = 1; // Benar
                        }

                        // 5. Simpan ke Log Praktek Keuangan
                        PraktekKeuangan::create([
                            'idUsers' => Auth::id(),
                            'idAkunKeuangan' => $record->id,
                            'table_name' => 'akun_keuangan',
                            'title' => 'Praktek Akun Keuangan',
                            'answer' => "Kategori: $category | Sub Kategori: $detailCategory",
                            'status_answer' => $statusAnswer,
                        ]);

                        return $record;
                    });
                }),
        ];
    }
}