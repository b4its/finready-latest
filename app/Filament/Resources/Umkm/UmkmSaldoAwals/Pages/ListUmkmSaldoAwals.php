<?php

namespace App\Filament\Resources\Umkm\UmkmSaldoAwals\Pages;

use App\Filament\Resources\Umkm\UmkmSaldoAwals\UmkmSaldoAwalResource;
use App\Models\DetailAkunKeuangan;
use App\Models\PraktekKeuangan;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListUmkmSaldoAwals extends ListRecords
{
    protected static string $resource = UmkmSaldoAwalResource::class;
    protected static ?string $title = "Daftar Saldo Awal";

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambahkan Saldo Awal")
                ->mutateFormDataUsing(function (array $data): array {;
                    $data['tipe'] = 1; // Indikator data praktek pengguna
                    return $data;
                })
                ->using(function (array $data, string $model): Model {
                    return DB::transaction(function () use ($data, $model) {
                        
                        // 1. Ambil input ID Detail Akun
                        $idDetailAkunKeuangan = $data['idDetailAkunKeuangan'];

                        // 2. Bersihkan format ribuan dari input (karena menggunakan mask RawJs)
                        $inputDebet = (float) str_replace(['.', ','], ['', '.'], $data['debet'] ?? 0);
                        $inputKredit = (float) str_replace(['.', ','], ['', '.'], $data['kredit'] ?? 0);

                        // 3. Cari idAkunKeuangan dari idDetailAkunKeuangan yang di-select di form
                        $selectedDetail = DetailAkunKeuangan::find($idDetailAkunKeuangan);
                        $idAkunKeuangan = $selectedDetail ? $selectedDetail->idAkunKeuangan : null;

                        // 4. Tarik kunci jawaban dari tabel referensi (tipe = 0, idUsers = null)
                        $kunciJawaban = DetailAkunKeuangan::where('idAkunKeuangan', $idAkunKeuangan)
                            ->where('tipe', 0)
                            ->whereNull('idUsers')
                            ->first();

                        // 5. Validasi Jawaban
                        $statusAnswer = 0;
                        $answerLog = ""; // Untuk menyimpan string jawaban ke kolom 'answer'

                        if ($kunciJawaban) {
                            if ($kunciJawaban->is_debet === 'D') {
                                // Benar jika referensi Debet, pengguna isi Debet >= 1, dan Kredit = 0
                                if ($inputDebet >= 1 && $inputKredit == 0) {
                                    $statusAnswer = 1;
                                }
                                $answerLog = "D: $inputDebet | K: $inputKredit";
                            } elseif ($kunciJawaban->is_debet === 'K') {
                                // Benar jika referensi Kredit, pengguna isi Kredit >= 1, dan Debet = 0
                                if ($inputKredit >= 1 && $inputDebet == 0) {
                                    $statusAnswer = 1;
                                }
                                $answerLog = "D: $inputDebet | K: $inputKredit";
                            }
                        }

                        // 6. Buat Record Utama (Asumsi field tabel SaldoAwal: idUsers, idDetailAkunKeuangan, debet, kredit, tipe)
                        // Sesuaikan nama key form ini dengan kolom di tabel model Saldo Awal Anda jika berbeda
                        $record = $model::create([
                            'idUsers' => Auth::id(),
                            'idDetailAkunKeuangan' => $idDetailAkunKeuangan,
                            'debet' => $inputDebet,
                            'kredit' => $inputKredit,
                            'tipe' => $data['tipe'],
                        ]);

                        // 7. Simpan ke Log Praktek Keuangan
                        PraktekKeuangan::create([
                            'idUsers' => Auth::id(),
                            'idAkunKeuangan' => $idAkunKeuangan,
                            'idDetailAkunKeuangan' => $data['idDetailAkunKeuangan'],
                            'idSaldoAwal' => $record['id'],
                            'idJurnalUmum' => null,
                            'table_name' => 'saldo_awal', // Ganti string ini jika nama tabel fisik saldo awal Anda berbeda
                            'title' => 'Praktek Saldo Awal',
                            'answer' => substr($answerLog, 0, 255),
                            'status_answer' => $statusAnswer,
                        ]);

                        return $record;
                    });
                }),
        ];
    }
}